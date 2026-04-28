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
            $cust1 = Party::firstOrCreate(['email' => 'john@example.com'], [
                'name' => 'John Doe',
                'type' => 'customer',
                'phone' => '9876543210',
                'gstin' => '07AAAAA0000A1Z5'
            ]);
            $vend1 = Party::firstOrCreate(['email' => 'sales@techsupplies.com'], [
                'name' => 'Tech Supplies Inc',
                'type' => 'vendor',
                'phone' => '1122334455',
                'gstin' => '08BBBBB1111B2Z6'
            ]);

            // 7. Products
            $macbook = Product::firstOrCreate(['sku' => 'MBP14-001'], [
                'name' => 'MacBook Pro 14',
                'hsn_code_id' => $hsnComp->id,
                'tax_rate_id' => $gst18->id,
                'brand_id' => $apple->id,
                'category_id' => $electronics->id,
                'sub_category_id' => $laptops->id,
                'unit' => 'pcs',
                'cost_price' => 150000,
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
                'cost_price' => 60000,
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
        });
    }
}
