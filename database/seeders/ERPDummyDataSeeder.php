<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TaxRate;
use App\Models\HsnCode;
use App\Models\Warehouse;
use App\Models\Party;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\LedgerEntry;
use App\Models\StockMovement;
use App\Models\OrderReturn;
use App\Models\ReturnItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Support\Facades\DB;

class ERPDummyDataSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function() {
            // 1. Tax Rates
            $gst18 = TaxRate::firstOrCreate(['name' => 'GST 18%'], ['cgst' => 9.00, 'sgst' => 9.00, 'igst' => 18.00]);
            $gst12 = TaxRate::firstOrCreate(['name' => 'GST 12%'], ['cgst' => 6.00, 'sgst' => 6.00, 'igst' => 12.00]);
            $gst5 = TaxRate::firstOrCreate(['name' => 'GST 5%'], ['cgst' => 2.50, 'sgst' => 2.50, 'igst' => 5.00]);

            // 2. HSN Codes
            $hsnComp = HsnCode::firstOrCreate(['code' => '8471'], ['description' => 'Computers', 'tax_rate_id' => $gst18->id]);
            $hsnPhone = HsnCode::firstOrCreate(['code' => '8517'], ['description' => 'Mobile Phones', 'tax_rate_id' => $gst12->id]);

            // 3. Brands
            $apple = Brand::firstOrCreate(['name' => 'Apple']);
            $samsung = Brand::firstOrCreate(['name' => 'Samsung']);
            $dell = Brand::firstOrCreate(['name' => 'Dell']);

            // 4. Categories & SubCategories
            $electronics = Category::firstOrCreate(['name' => 'Electronics']);
            $laptops = SubCategory::firstOrCreate(['name' => 'Laptops', 'category_id' => $electronics->id]);
            $smartphones = SubCategory::firstOrCreate(['name' => 'Smartphones', 'category_id' => $electronics->id]);

            // 5. Warehouses
            $mainWh = Warehouse::firstOrCreate(['code' => 'WH001'], ['name' => 'Main Warehouse', 'state' => 'Delhi']);
            $retailWh = Warehouse::firstOrCreate(['code' => 'WH002'], ['name' => 'Retail Outlet', 'state' => 'Maharashtra']);

            // 6. Parties
            $cust1 = Party::firstOrCreate(
                ['party_code' => 'CUST-000001'],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'name' => 'John Doe',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'type' => 'customer',
                    'mobile' => '9876543210',
                    'email' => 'john@example.com',
                    'gstin' => '07AAAAA0000A1Z5'
                ]
            );

            $vend1 = Party::firstOrCreate(
                ['party_code' => 'VEND-000001'],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'name' => 'Tech Supplies Inc',
                    'type' => 'vendor',
                    'mobile' => '1122334455',
                    'email' => 'sales@techsupplies.com',
                    'gstin' => '08BBBBB1111B2Z6'
                ]
            );

            // 7. Products
            $macbook = Product::firstOrCreate(['sku' => 'MBP14-001'], [
                'name' => 'MacBook Pro 14',
                'hsn_code_id' => $hsnComp->id,
                'tax_rate_id' => $gst18->id,
                'brand_id' => $apple->id,
                'category_id' => $electronics->id,
                'sub_category_id' => $laptops->id,
                'unit' => 'pcs',
                'purchase_price' => 150000,
                'selling_price' => 180000,
                'mrp' => 199000,
                'is_active' => true,
            ]);

            $galaxy = Product::firstOrCreate(['sku' => 'GS23-001'], [
                'name' => 'Galaxy S23',
                'hsn_code_id' => $hsnPhone->id,
                'tax_rate_id' => $gst12->id,
                'brand_id' => $samsung->id,
                'category_id' => $electronics->id,
                'sub_category_id' => $smartphones->id,
                'unit' => 'pcs',
                'purchase_price' => 60000,
                'selling_price' => 75000,
                'mrp' => 85000,
                'is_active' => true,
            ]);

            // 8. Product Variants
            $macbookGray = ProductVariant::firstOrCreate(['sku' => 'MBP14-GRAY-16'], [
                'product_id' => $macbook->id,
                'color' => 'Space Gray',
                'size' => '16GB',
                'attributes' => ['storage' => '512GB SSD'],
                'price' => 185000
            ]);

            // 9. Initial Stock (Opening)
            Stock::updateOrCreate(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id], ['quantity' => 5]);
            StockMovement::create(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id, 'type' => 'in', 'quantity' => 5, 'reference_type' => 'Opening Stock']);

            // 10. Purchase Cycle (Restock)
            $poPrice = 150000;
            $poQty = 10;
            $poSubtotal = $poPrice * $poQty;
            $poTax = $poSubtotal * 0.18;
            $poTotal = $poSubtotal + $poTax;

            $po = Order::create([
                'order_number' => 'PO-' . time(),
                'party_id' => $vend1->id,
                'warehouse_id' => $mainWh->id,
                'type' => 'purchase',
                'order_date' => now()->subDays(5),
                'sub_total' => $poSubtotal,
                'tax_amount' => $poTax,
                'total_amount' => $poTotal,
                'status' => 'completed'
            ]);
            $po->items()->create(['product_id' => $macbook->id, 'quantity' => $poQty, 'unit_price' => $poPrice, 'tax_amount' => $poTax, 'total_price' => $poTotal]);

            // Stock update for PO
            $stock = Stock::firstOrCreate(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id], ['quantity' => 0]);
            $stock->increment('quantity', $poQty);
            StockMovement::create(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id, 'type' => 'in', 'quantity' => $poQty, 'reference_type' => 'Order', 'reference_id' => $po->id]);

            // Purchase Invoice
            $pInv = Invoice::create([
                'invoice_number' => 'PINV-' . time(),
                'order_id' => $po->id,
                'party_id' => $vend1->id,
                'invoice_date' => now()->subDays(5),
                'sub_total' => $poSubtotal,
                'tax_amount' => $poTax,
                'total_amount' => $poTotal,
                'status' => 'paid'
            ]);

            // Purchase Ledger
            LedgerEntry::create([
                'party_id' => $vend1->id,
                'entry_date' => now()->subDays(5),
                'description' => 'Purchase Invoice #' . $pInv->invoice_number,
                'type' => 'credit',
                'amount' => $poTotal,
                'reference_type' => 'Invoice',
                'reference_id' => $pInv->id
            ]);

            // 11. Sales Cycle
            $soPrice = 180000;
            $soQty = 2;
            $soSubtotal = $soPrice * $soQty;
            $soTax = $soSubtotal * 0.18;
            $soTotal = $soSubtotal + $soTax;

            $so = Order::create([
                'order_number' => 'SO-' . time(),
                'party_id' => $cust1->id,
                'warehouse_id' => $mainWh->id,
                'type' => 'sale',
                'order_date' => now()->subDays(2),
                'sub_total' => $soSubtotal,
                'tax_amount' => $soTax,
                'total_amount' => $soTotal,
                'status' => 'completed'
            ]);
            $so->items()->create(['product_id' => $macbook->id, 'quantity' => $soQty, 'unit_price' => $soPrice, 'tax_amount' => $soTax, 'total_price' => $soTotal]);

            // Stock update for SO
            $stock = Stock::where(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id])->first();
            $stock->decrement('quantity', $soQty);
            StockMovement::create(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id, 'type' => 'out', 'quantity' => $soQty, 'reference_type' => 'Order', 'reference_id' => $so->id]);

            // Sale Invoice
            $sInv = Invoice::create([
                'invoice_number' => 'SINV-' . time(),
                'order_id' => $so->id,
                'party_id' => $cust1->id,
                'invoice_date' => now()->subDays(2),
                'sub_total' => $soSubtotal,
                'tax_amount' => $soTax,
                'total_amount' => $soTotal,
                'status' => 'partial'
            ]);

            // Sale Ledger
            LedgerEntry::create([
                'party_id' => $cust1->id,
                'entry_date' => now()->subDays(2),
                'description' => 'Sale Invoice #' . $sInv->invoice_number,
                'type' => 'debit',
                'amount' => $soTotal,
                'reference_type' => 'Invoice',
                'reference_id' => $sInv->id
            ]);

            // 12. Payment
            $payAmount = 100000;
            $pay = Payment::create([
                'invoice_id' => $sInv->id,
                'party_id' => $cust1->id,
                'payment_number' => 'PAY-' . time(),
                'payment_date' => now()->subDay(),
                'amount' => $payAmount,
                'payment_method' => 'bank',
                'status' => 'completed'
            ]);

            LedgerEntry::create([
                'party_id' => $cust1->id,
                'entry_date' => now()->subDay(),
                'description' => 'Payment for Invoice #' . $sInv->invoice_number,
                'type' => 'credit',
                'amount' => $payAmount,
                'reference_type' => 'Payment',
                'reference_id' => $pay->id
            ]);

            // 13. Stock Transfer
            $transfer = StockTransfer::create([
                'from_warehouse_id' => $mainWh->id,
                'to_warehouse_id' => $retailWh->id,
                'product_id' => $macbook->id,
                'quantity' => 1,
                'transfer_date' => now(),
                'status' => 'completed',
                'notes' => 'Stock for retail display'
            ]);
            $transfer->items()->create(['product_id' => $macbook->id, 'quantity' => 1]);
            
            Stock::where(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id])->first()->decrement('quantity', 1);
            Stock::updateOrCreate(['product_id' => $macbook->id, 'warehouse_id' => $retailWh->id], ['quantity' => DB::raw('quantity + 1')]);
            
            StockMovement::create(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id, 'type' => 'out', 'quantity' => 1, 'reference_type' => 'Stock Transfer', 'reference_id' => $transfer->id]);
            StockMovement::create(['product_id' => $macbook->id, 'warehouse_id' => $retailWh->id, 'type' => 'in', 'quantity' => 1, 'reference_type' => 'Stock Transfer', 'reference_id' => $transfer->id]);

            // 14. Return
            $ret = OrderReturn::create([
                'order_id' => $so->id,
                'party_id' => $cust1->id,
                'return_number' => 'RET-' . time(),
                'return_date' => now(),
                'status' => 'completed',
                'reason' => 'Minor scratches'
            ]);
            $ret->items()->create(['product_id' => $macbook->id, 'quantity' => 1]);
            
            Stock::where(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id])->first()->increment('quantity', 1);
            StockMovement::create(['product_id' => $macbook->id, 'warehouse_id' => $mainWh->id, 'type' => 'in', 'quantity' => 1, 'reference_type' => 'Return', 'reference_id' => $ret->id]);

            // ==========================================
            // WMS & FULL ENTERPRISE DUMMY DATA
            // ==========================================

            // System Settings
            \App\Models\SystemSetting::firstOrCreate(['key' => 'tally_sync_mode'], ['value' => 'manual', 'group' => 'tally']);
            \App\Models\SystemSetting::firstOrCreate(['key' => 'tally_url'], ['value' => 'http://localhost:9000', 'group' => 'tally']);

            // WMS Setup: Batch
            $batch1 = \App\Models\StockBatch::firstOrCreate(
                ['batch_no' => 'BATCH-001', 'product_id' => $galaxy->id, 'warehouse_id' => $mainWh->id], 
                ['expiry_date' => now()->addYears(2), 'qty' => 50]
            );

            // Add Stock for Galaxy
            Stock::updateOrCreate(['product_id' => $galaxy->id, 'warehouse_id' => $mainWh->id], ['quantity' => 50]);

            // Create WMS specific Sales Order
            $wmsOrder = Order::create([
                'order_number' => 'WMS-' . time(),
                'party_id' => $cust1->id,
                'warehouse_id' => $mainWh->id,
                'type' => 'sale',
                'order_date' => now()->subDay(),
                'sub_total' => 75000 * 5,
                'tax_amount' => (75000 * 5) * 0.12,
                'total_amount' => (75000 * 5) * 1.12,
                'status' => 'allocated', // Setting to allocated to test picking/packing
                'confirmed_at' => now()->subHours(5),
                'allocated_at' => now()->subHours(4),
            ]);
            $wmsOrderItem = $wmsOrder->items()->create(['product_id' => $galaxy->id, 'quantity' => 5, 'unit_price' => 75000, 'tax_amount' => (75000 * 5) * 0.12, 'total_price' => (75000 * 5) * 1.12]);

            // 1. Allocation
            \App\Models\OrderAllocation::create([
                'order_id' => $wmsOrder->id,
                'order_item_id' => $wmsOrderItem->id,
                'product_id' => $galaxy->id,
                'warehouse_id' => $mainWh->id,
                'batch_id' => $batch1->id,
                'allocated_qty' => 5,
                'status' => 'allocated'
            ]);

            // 2. Pick List
            $pickList = \App\Models\PickList::create([
                'pick_list_number' => 'PL-' . time(),
                'order_id' => $wmsOrder->id,
                'warehouse_id' => $mainWh->id,
                'status' => 'pending',
                'assigned_to' => 1 // Assuming User ID 1 exists
            ]);
            $pickList->items()->create([
                'product_id' => $galaxy->id,
                'order_item_id' => $wmsOrderItem->id,
                'batch_id' => $batch1->id,
                'requested_qty' => 5,
                'picked_qty' => 0
            ]);

            // 3. Shipment (For another order to show in transit)
            $shippedOrder = Order::create([
                'order_number' => 'SHP-' . time(),
                'party_id' => $cust1->id,
                'warehouse_id' => $mainWh->id,
                'type' => 'sale',
                'order_date' => now()->subDays(3),
                'sub_total' => 75000,
                'tax_amount' => 75000 * 0.12,
                'total_amount' => 75000 * 1.12,
                'status' => 'shipped',
                'confirmed_at' => now()->subDays(3),
                'allocated_at' => now()->subDays(3),
                'picking_at' => now()->subDays(2),
                'picked_at' => now()->subDays(2),
                'packing_at' => now()->subDays(2),
                'packed_at' => now()->subDays(2),
                'shipped_at' => now()->subDays(1),
            ]);
            $shippedOrderItem = $shippedOrder->items()->create(['product_id' => $galaxy->id, 'quantity' => 1, 'unit_price' => 75000, 'tax_amount' => 75000 * 0.12, 'total_price' => 75000 * 1.12]);

            $shipment = \App\Models\Shipment::create([
                'order_id' => $shippedOrder->id,
                'shipment_number' => 'SHP-NO-' . time(),
                'tracking_number' => 'TRK' . time(),
                'carrier' => 'BlueDart',
                'status' => 'in_transit',
                'shipped_at' => now()->subDays(1),
            ]);
            $package = \App\Models\Package::create([
                'order_id' => $shippedOrder->id,
                'package_number' => 'PKG-' . time(),
                'weight' => 1.5,
                'dimensions' => '10x10x5'
            ]);
            $package->items()->create([
                'order_item_id' => $shippedOrderItem->id,
                'product_id' => $galaxy->id,
                'quantity' => 1
            ]);
            $shipment->trackingEvents()->create([
                'status' => 'in_transit',
                'location' => 'Mumbai Hub',
                'description' => 'Package has arrived at transit hub',
                'event_at' => now()
            ]);

            // 4. Backorder
            $backorderOrder = Order::create([
                'order_number' => 'BO-' . time(),
                'party_id' => $cust1->id,
                'warehouse_id' => $mainWh->id,
                'type' => 'sale',
                'order_date' => now(),
                'sub_total' => 180000 * 10,
                'tax_amount' => (180000 * 10) * 0.18,
                'total_amount' => (180000 * 10) * 1.18,
                'status' => 'backordered',
                'confirmed_at' => now(),
            ]);
            $boItem = $backorderOrder->items()->create(['product_id' => $macbook->id, 'quantity' => 10, 'unit_price' => 180000, 'tax_amount' => (180000 * 10) * 0.18, 'total_price' => (180000 * 10) * 1.18]);
            
            \App\Models\Backorder::create([
                'backorder_number' => 'BO-NUM-' . time(),
                'order_id' => $backorderOrder->id,
                'order_item_id' => $boItem->id,
                'product_id' => $macbook->id,
                'warehouse_id' => $mainWh->id,
                'pending_qty' => 10, // Assuming 0 stock
                'fulfilled_qty' => 0,
                'status' => 'pending'
            ]);

            // 5. Accounting Transaction (Double Entry)
            $accTrans = \App\Models\AccountingTransaction::create([
                'transaction_number' => 'JRN-' . time(),
                'transaction_date' => now(),
                'type' => 'journal',
                'reference_type' => 'Order',
                'reference_id' => $so->id,
                'narration' => 'Sales Revenue Recording',
                'total_amount' => $soTotal
            ]);

            // Create Ledgers if not exist
            $salesLedger = \App\Models\Ledger::firstOrCreate(['name' => 'Sales Revenue'], ['type' => 'revenue', 'group' => 'Direct Incomes']);
            $cgstLedger = \App\Models\Ledger::firstOrCreate(['name' => 'CGST Payable'], ['type' => 'liability', 'group' => 'Duties & Taxes']);
            $sgstLedger = \App\Models\Ledger::firstOrCreate(['name' => 'SGST Payable'], ['type' => 'liability', 'group' => 'Duties & Taxes']);
            $debtorLedger = \App\Models\Ledger::firstOrCreate(['name' => 'Sundry Debtors - ' . $cust1->name], ['type' => 'asset', 'group' => 'Sundry Debtors']);

            $accTrans->entries()->create(['ledger_id' => $debtorLedger->id, 'debit' => $soTotal, 'entry_date' => now()]);
            $accTrans->entries()->create(['ledger_id' => $salesLedger->id, 'credit' => $soSubtotal, 'entry_date' => now()]);
            $accTrans->entries()->create(['ledger_id' => $cgstLedger->id, 'credit' => $soTax / 2, 'entry_date' => now()]);
            $accTrans->entries()->create(['ledger_id' => $sgstLedger->id, 'credit' => $soTax / 2, 'entry_date' => now()]);

            // 6. Tally Sync Log
            \App\Models\TallySyncLog::create([
                'reference_type' => 'AccountingTransaction',
                'reference_id' => $accTrans->id,
                'voucher_type' => 'sales_voucher',
                'payload' => '<ENVELOPE><HEADER><TALLYREQUEST>Import Data</TALLYREQUEST></HEADER><BODY><IMPORTDATA><REQUESTDESC><REPORTNAME>Vouchers</REPORTNAME></REQUESTDESC><REQUESTDATA><TALLYMESSAGE xmlns:UDF="TallyUDF"><VOUCHER><DATE>20260429</DATE><NARRATION>Dummy Sales Revenue</NARRATION></VOUCHER></TALLYMESSAGE></REQUESTDATA></IMPORTDATA></BODY></ENVELOPE>',
                'status' => 'failed',
                'error_message' => 'Connection refused: Tally ERP is not reachable at localhost:9000',
                'retry_count' => 3,
                'last_attempt_at' => now()
            ]);        });
    }
}
