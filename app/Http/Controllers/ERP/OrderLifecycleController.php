<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderLifecycleController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function confirm(Order $order)
    {
        try {
            $this->orderService->confirm($order);
            $order->refresh(); // ✅ ensure latest state

            return redirect()
                ->route('erp.orders.show', $order)
                ->with('success', "Order #{$order->order_number} confirmed and stock reserved.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function allocate(Order $order)
    {
        try {
            $this->orderService->allocate($order);
            $order->refresh();

            return redirect()
                ->route('erp.orders.show', $order)
                ->with('success', "Order #{$order->order_number} allocated to batches/bins.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deliver(Order $order)
    {
        try {
            $this->orderService->deliver($order);
            $order->refresh();

            return redirect()
                ->route('erp.orders.show', $order)
                ->with('success', "Order #{$order->order_number} marked as delivered.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function close(Order $order)
    {
        try {
            $this->orderService->close($order);
            $order->refresh();

            return redirect()
                ->route('erp.orders.show', $order)
                ->with('success', "Order #{$order->order_number} closed.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Order $order)
    {
        try {
            $this->orderService->cancel($order, $request->input('reason', ''));
            $order->refresh();

            return redirect()
                ->route('erp.orders.show', $order)
                ->with('success', "Order #{$order->order_number} cancelled.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function hold(Order $order)
    {
        try {
            $this->orderService->hold($order);
            $order->refresh();

            return redirect()
                ->route('erp.orders.show', $order)
                ->with('success', "Order #{$order->order_number} put on hold.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}