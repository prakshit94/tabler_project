<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Activity;

$orderNumber = 'ORD-9FI5VLFV';
$order = Order::where('order_number', $orderNumber)->with(['items.product', 'warehouse', 'party'])->first();

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$workflow = [
    'order' => [
        'id' => $order->id,
        'number' => $order->order_number,
        'status' => $order->status,
        'total' => $order->total_amount,
        'date' => $order->order_date,
    ],
    'customer' => [
        'name' => $order->party->name,
        'mobile' => $order->party->mobile,
    ],
    'items' => $order->items->map(function($item) {
        return [
            'product' => $item->product->name,
            'quantity' => $item->quantity,
            'price' => $item->unit_price,
            'total' => $item->total_price,
        ];
    }),
    'warehouse' => $order->warehouse->name,
    'logs' => \Spatie\Activitylog\Models\Activity::forSubject($order)->get()->map(function($log) {
        return [
            'description' => $log->description,
            'causer' => $log->causer?->name ?? 'System',
            'created_at' => $log->created_at->toDateTimeString(),
        ];
    }),
];

echo json_encode($workflow, JSON_PRETTY_PRINT);
