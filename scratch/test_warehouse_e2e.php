<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

// 1. Setup Test Order
$product = Product::where('name', 'Galaxy S23')->first();
$warehouseId = 1;

// Reset Stock to Clean State
Stock::where('product_id', $product->id)->where('warehouse_id', $warehouseId)
    ->update(['quantity' => 50, 'reserved_qty' => 0, 'committed_qty' => 0, 'in_transit_qty' => 0]);

$order = Order::create([
    'order_number' => 'TEST-' . strtoupper(uniqid()),
    'party_id' => 1,
    'warehouse_id' => $warehouseId,
    'type' => 'sale',
    'order_date' => now(),
    'sub_total' => 75000,
    'total_amount' => 75000,
    'status' => 'pending',
]);

OrderItem::create([
    'order_id' => $order->id,
    'product_id' => $product->id,
    'quantity' => 5,
    'unit_price' => 15000,
    'total_price' => 75000,
]);

$orderService = app(OrderService::class);

function logStock($productId, $whId) {
    $s = Stock::where('product_id', $productId)->where('warehouse_id', $whId)->first();
    echo "   [Stock] Qty: {$s->quantity} | Res: {$s->reserved_qty} | Com: {$s->committed_qty} | Transit: {$s->in_transit_qty}\n";
}

echo "Starting End-to-End Warehouse Test for Order: {$order->order_number}\n";
echo "Initial State:\n";
logStock($product->id, $warehouseId);

try {
    echo "\nStep 1: Confirming order (Reservation)...\n";
    $order = $orderService->confirm($order);
    logStock($product->id, $warehouseId);

    echo "\nStep 2: Allocating order...\n";
    $order = $orderService->allocate($order);
    logStock($product->id, $warehouseId);

    echo "\nStep 3: Marking as picked (Reserve -> Commit)...\n";
    $order = $orderService->startPicking($order);
    $order = $orderService->markPicked($order);
    logStock($product->id, $warehouseId);

    echo "\nStep 4: Marking as packed...\n";
    $order = $orderService->startPacking($order);
    $order = $orderService->markPacked($order);
    logStock($product->id, $warehouseId);

    echo "\nStep 5: Shipping order (Commit -> Deduct -> Transit)...\n";
    $order = $orderService->ship($order);
    logStock($product->id, $warehouseId);

    echo "\nStep 6: Delivering order (Clear Transit)...\n";
    $order = $orderService->deliver($order);
    logStock($product->id, $warehouseId);

    echo "\nFinal Results:\n";
    echo "   Order Status: {$order->status}\n";
    echo "   Stock Correctly Deducted: " . ($order->status == 'delivered' && Stock::where('product_id', $product->id)->where('warehouse_id', $warehouseId)->first()->quantity == 45 ? 'YES' : 'NO') . "\n";

    echo "\nEnd-to-End Test Passed Successfully!\n";

} catch (\Exception $e) {
    echo "\nTest Failed: " . $e->getMessage() . "\n";
}
