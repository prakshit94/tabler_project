<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

$orderNumber = 'ORD-9FI5VLFV';
$order = Order::where('order_number', $orderNumber)->first();

if (!$order) {
    echo "Order not found\n";
    exit;
}

$orderService = app(OrderService::class);

function logStock($productId) {
    $stocks = Stock::where('product_id', $productId)->get();
    foreach($stocks as $s) {
        echo "   [Stock] Wh: {$s->warehouse_id} | Qty: {$s->quantity} | Res: {$s->reserved_qty} | Com: {$s->committed_qty}\n";
    }
}

echo "Initial State: {$order->status}\n";
$productId = $order->items->first()->product_id;
logStock($productId);

try {
    // 1. Confirm
    echo "\nStep 1: Confirming order...\n";
    $order = $orderService->confirm($order);
    echo "   New Status: {$order->status}\n";
    logStock($productId);

    // 2. Allocate
    echo "\nStep 2: Allocating order...\n";
    $order = $orderService->allocate($order);
    echo "   New Status: {$order->status}\n";
    logStock($productId);

    // 3. Picking
    echo "\nStep 3: Starting picking...\n";
    $order = $orderService->startPicking($order);
    echo "   New Status: {$order->status}\n";
    
    echo "\nStep 4: Marking as picked...\n";
    $order = $orderService->markPicked($order);
    echo "   New Status: {$order->status}\n";
    logStock($productId);

    // 4. Packing
    echo "\nStep 5: Starting packing...\n";
    $order = $orderService->startPacking($order);
    echo "   New Status: {$order->status}\n";

    echo "\nStep 6: Marking as packed...\n";
    $order = $orderService->markPacked($order);
    echo "   New Status: {$order->status}\n";

    // 5. Shipping
    echo "\nStep 7: Shipping order...\n";
    $order = $orderService->ship($order);
    echo "   New Status: {$order->status}\n";
    logStock($productId);

    echo "\nWorkflow Test Completed Successfully!\n";

} catch (\Exception $e) {
    echo "\nError during workflow test: " . $e->getMessage() . "\n";
}
