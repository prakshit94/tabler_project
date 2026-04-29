<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Backorder;
use App\Models\Stock;
use App\Events\OrderConfirmed;
use App\Events\StockReserved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    // Valid transitions for the state machine
    const TRANSITIONS = [
        'pending'          => ['confirmed', 'cancelled'],
        'draft'            => ['confirmed', 'cancelled'],
        'confirmed'        => ['allocated', 'on_hold', 'cancelled', 'backordered'],
        'allocated'        => ['picking', 'on_hold', 'cancelled'],
        'picking'          => ['picked', 'on_hold'],
        'picked'           => ['packing'],
        'packing'          => ['packed'],
        'packed'           => ['shipped'],
        'shipped'          => ['in_transit', 'delivered'],
        'in_transit'       => ['delivered'],
        'delivered'        => ['closed', 'return_initiated'],
        'closed'           => [],
        'on_hold'          => ['confirmed', 'allocated', 'picking', 'cancelled'],
        'backordered'      => ['allocated', 'cancelled'],
        'cancelled'        => [],
        'return_initiated' => ['return_completed'],
        'return_completed' => [],
        'partial'          => ['allocated', 'picking'],
    ];

    public function __construct(
        private InventoryService $inventory,
        private AllocationService $allocation,
    ) {}

    /**
     * Confirm an order: validate stock, reserve inventory, create backorders if needed.
     */
    public function confirm(Order $order): Order
    {
        $this->assertTransition($order, 'confirmed');

        DB::transaction(function () use ($order) {
            $order->load('items');
            $hasBackorder = false;

            foreach ($order->items as $item) {
                $available = $this->inventory->getAvailableQty($item->product_id, $order->warehouse_id);
                $toReserve = min($item->quantity, $available);
                $shortage  = $item->quantity - $toReserve;

                if ($toReserve > 0) {
                    $stock = Stock::where('product_id', $item->product_id)
                        ->where('warehouse_id', $order->warehouse_id)->first();
                    if ($stock) {
                        $this->inventory->reserve($order);
                    }
                }

                if ($shortage > 0) {
                    $hasBackorder = true;
                    Backorder::create([
                        'backorder_number' => 'BO-' . strtoupper(uniqid()),
                        'order_id'         => $order->id,
                        'order_item_id'    => $item->id,
                        'product_id'       => $item->product_id,
                        'warehouse_id'     => $order->warehouse_id,
                        'pending_qty'      => $shortage,
                        'status'           => 'pending',
                    ]);
                }
            }

            $newStatus = $hasBackorder ? 'backordered' : 'confirmed';
            $order->update([
                'status'       => $newStatus,
                'confirmed_at' => now(),
            ]);

            event(new OrderConfirmed($order));
            event(new StockReserved($order));
        });

        return $order->fresh();
    }

    /**
     * Allocate order to batches/bins.
     */
    public function allocate(Order $order): Order
    {
        $this->assertTransition($order, 'allocated');

        DB::transaction(function () use ($order) {
            $this->allocation->allocate($order);
            $order->update([
                'status'       => 'allocated',
                'allocated_at' => now(),
            ]);
            event(new \App\Events\OrderAllocated($order));
        });

        return $order->fresh();
    }

    /**
     * Transition to picking stage.
     */
    public function startPicking(Order $order): Order
    {
        $this->assertTransition($order, 'picking');
        $order->update(['status' => 'picking', 'picking_at' => now()]);
        return $order->fresh();
    }

    /**
     * Mark order as picked — reserved → committed.
     */
    public function markPicked(Order $order): Order
    {
        $this->assertTransition($order, 'picked');

        DB::transaction(function () use ($order) {
            $this->inventory->commit($order);
            $order->update(['status' => 'picked', 'picked_at' => now()]);
            event(new \App\Events\OrderPicked($order));
        });

        return $order->fresh();
    }

    /**
     * Transition to packing stage.
     */
    public function startPacking(Order $order): Order
    {
        $this->assertTransition($order, 'packing');
        $order->update(['status' => 'packing', 'packing_at' => now()]);
        return $order->fresh();
    }

    /**
     * Mark order as packed.
     */
    public function markPacked(Order $order): Order
    {
        $this->assertTransition($order, 'packed');
        $order->update(['status' => 'packed', 'packed_at' => now()]);
        event(new \App\Events\OrderPacked($order));
        return $order->fresh();
    }

    /**
     * Ship the order — committed → deducted → in_transit.
     */
    public function ship(Order $order): Order
    {
        $this->assertTransition($order, 'shipped');

        DB::transaction(function () use ($order) {
            $this->inventory->ship($order);
            $order->update(['status' => 'shipped', 'shipped_at' => now()]);
            event(new \App\Events\OrderShipped($order));
        });

        return $order->fresh();
    }

    /**
     * Mark order as delivered.
     */
    public function deliver(Order $order): Order
    {
        $this->assertTransition($order, 'delivered');

        DB::transaction(function () use ($order) {
            $this->inventory->deliver($order);
            $order->update(['status' => 'delivered', 'delivered_at' => now()]);
            event(new \App\Events\OrderDelivered($order));
        });

        return $order->fresh();
    }

    /**
     * Close order — locks it.
     */
    public function close(Order $order): Order
    {
        $this->assertTransition($order, 'closed');
        $order->update(['status' => 'closed', 'closed_at' => now()]);
        return $order->fresh();
    }

    /**
     * Cancel an order — release reserved stock.
     */
    public function cancel(Order $order, string $reason = ''): Order
    {
        $this->assertTransition($order, 'cancelled');

        DB::transaction(function () use ($order, $reason) {
            if (in_array($order->status, ['confirmed', 'allocated', 'backordered', 'on_hold'])) {
                $this->inventory->release($order);
            }
            $order->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
                'notes'        => $order->notes . "\nCancelled: {$reason}",
            ]);
        });

        return $order->fresh();
    }

    /**
     * Put order on hold.
     */
    public function hold(Order $order): Order
    {
        $this->assertTransition($order, 'on_hold');
        $order->update(['status' => 'on_hold']);
        return $order->fresh();
    }

    /**
     * Check if a transition is valid.
     */
    public function canTransitionTo(Order $order, string $newStatus): bool
    {
        $allowed = self::TRANSITIONS[$order->status] ?? [];
        return in_array($newStatus, $allowed);
    }

    private function assertTransition(Order $order, string $newStatus): void
    {
        if (!$this->canTransitionTo($order, $newStatus)) {
            throw new \RuntimeException(
                "Invalid order transition: [{$order->status}] → [{$newStatus}] for Order #{$order->order_number}"
            );
        }
    }
}
