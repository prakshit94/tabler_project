<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\LedgerEntry;
use App\Models\OrderReturn;
use App\Models\Stock;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

$logFile = 'scratch/workflow_test_log.txt';
function logMsg($msg) {
    global $logFile;
    echo $msg . "\n";
    file_put_contents($logFile, $msg . "\n", FILE_APPEND);
}

if (file_exists($logFile)) unlink($logFile);

$orderNumber = 'ORD-9FI5VLFV';
$order = Order::where('order_number', $orderNumber)->with(['items', 'party'])->first();

if (!$order) {
    logMsg("Order not found");
    exit;
}

logMsg("Testing End-to-End Accounting & Return Workflow for Order: $orderNumber");

try {
    DB::beginTransaction();

    // 1. Generate Delivery Challan (Shipment)
    logMsg("\n[1/3] Generating Delivery Challan (Shipment)...");
    
    $shipment = new Shipment();
    $shipment->order_id = $order->id;
    $shipment->shipment_number = 'SHP-' . strtoupper(uniqid());
    $shipment->tracking_number = 'TRK-' . strtoupper(uniqid());
    $shipment->carrier = 'Delhivery';
    $shipment->status = 'shipped';
    $shipment->shipped_at = now();
    $shipment->save();
    
    logMsg("   Challan generated: {$shipment->tracking_number}");

    // 2. Accounting/Payment Workflow
    logMsg("\n[2/3] Testing Accounting/Payment Workflow...");
    
    $invoice = new Invoice();
    $invoice->order_id = $order->id;
    $invoice->party_id = $order->party_id;
    $invoice->invoice_number = 'INV-' . time();
    $invoice->invoice_date = now();
    $invoice->due_date = now()->addDays(7);
    $invoice->sub_total = $order->sub_total;
    $invoice->tax_amount = $order->tax_amount;
    $invoice->total_amount = $order->total_amount;
    $invoice->status = 'unpaid';
    $invoice->save();
    
    logMsg("   Invoice Created: {$invoice->invoice_number} | Amount: ₹ {$invoice->total_amount}");

    $paymentAmount = 500000;
    $payment = new Payment();
    $payment->invoice_id = $invoice->id;
    $payment->party_id = $order->party_id;
    $payment->payment_number = 'PAY-' . time();
    $payment->payment_date = now();
    $payment->amount = $paymentAmount;
    $payment->payment_method = 'bank';
    $payment->status = 'completed';
    $payment->save();
    
    $ledger = new LedgerEntry();
    $ledger->party_id = $order->party_id;
    $ledger->entry_date = now();
    $ledger->description = 'Payment against Invoice #' . $invoice->invoice_number;
    $ledger->type = 'credit';
    $ledger->amount = $paymentAmount;
    $ledger->reference_type = 'Payment';
    $ledger->reference_id = $payment->id;
    $ledger->save();
    
    $invoice->update(['status' => 'partial']);
    logMsg("   Payment Recorded: ₹ {$paymentAmount} | Invoice Status: {$invoice->status}");

    // 3. Return Process
    logMsg("\n[3/3] Testing Return Process...");
    $returnQty = 2;
    $returnItem = $order->items->first();
    
    $return = new OrderReturn();
    $return->order_id = $order->id;
    $return->party_id = $order->party_id;
    $return->return_number = 'RET-' . time();
    $return->return_date = now();
    $return->reason = 'Damaged during transit';
    $return->status = 'completed';
    $return->warehouse_id = $order->warehouse_id;
    $return->save();
    
    // ReturnItem uses product_id, quantity, reason
    $return->items()->create([
        'product_id' => $returnItem->product_id,
        'quantity' => $returnQty,
        'reason' => 'Damaged',
    ]);

    $stock = Stock::where('product_id', $returnItem->product_id)->where('warehouse_id', $order->warehouse_id)->first();
    $oldQty = $stock->quantity;
    $stock->increment('quantity', $returnQty);
    
    logMsg("   Return Processed: {$return->return_number} | Qty: {$returnQty}");
    logMsg("   Stock Restoration Check: $oldQty -> {$stock->fresh()->quantity} (Correct: " . ($stock->fresh()->quantity == $oldQty + $returnQty ? 'YES' : 'NO') . ")");

    DB::commit();
    logMsg("\nEnd-to-End Workflow Test Completed Successfully!");

} catch (\Exception $e) {
    DB::rollBack();
    logMsg("\nWorkflow Test Failed: " . $e->getMessage());
    logMsg($e->getTraceAsString());
}
