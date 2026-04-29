<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

/**
 * Handles WMS state machine transitions for orders.
 * All logic delegated to OrderService.
 */
class OrderLifecycleController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function confirm(Order $order)
    {
        try {
            $this->orderService->confirm($order);
            return redirect()->route('erp.orders.show', $order)->with('success', "Order #{$order->order_number} confirmed and stock reserved.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function allocate(Order $order)
    {
        try {
            $this->orderService->allocate($order);
            return redirect()->route('erp.orders.show', $order)->with('success', "Order #{$order->order_number} allocated to batches/bins.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deliver(Order $order)
    {
        try {
            $this->orderService->deliver($order);
            return redirect()->route('erp.orders.show', $order)->with('success', "Order #{$order->order_number} marked as delivered.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function close(Order $order)
    {
        try {
            $this->orderService->close($order);
            return redirect()->route('erp.orders.show', $order)->with('success', "Order #{$order->order_number} closed.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Order $order)
    {
        try {
            $this->orderService->cancel($order, $request->input('reason', ''));
            return redirect()->route('erp.orders.show', $order)->with('success', "Order #{$order->order_number} cancelled.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function hold(Order $order)
    {
        try {
            $this->orderService->hold($order);
            return redirect()->route('erp.orders.show', $order)->with('success', "Order #{$order->order_number} put on hold.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
