<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\InventoryLedger;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    /**
     * Reserve inventory when order is confirmed.
     * reserved_qty += qty
     */
    public function reserve(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->increment('reserved_qty', $item->quantity);
                $this->createLedgerEntry($item->product_id, $order->warehouse_id, null, 'reserve', $item->quantity, $stock->fresh()->quantity, 'Order', $order->id);
            }
        }
    }

    /**
     * Release reserved inventory (e.g. order cancelled).
     * reserved_qty -= qty
     */
    public function release(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('reserved_qty', $item->quantity);
                $this->createLedgerEntry($item->product_id, $order->warehouse_id, null, 'release', -$item->quantity, $stock->fresh()->quantity, 'Order', $order->id);
            }
        }
    }

    /**
     * Commit inventory when items are picked.
     * reserved_qty -= qty, committed_qty += qty
     */
    public function commit(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('reserved_qty', $item->quantity);
                $stock->increment('committed_qty', $item->quantity);
                $this->createLedgerEntry($item->product_id, $order->warehouse_id, null, 'commit', $item->quantity, $stock->fresh()->quantity, 'Order', $order->id);
            }
        }
    }

    /**
     * Ship inventory.
     * committed_qty -= qty, quantity -= qty, in_transit_qty += qty
     */
    public function ship(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('committed_qty', $item->quantity);
                $stock->decrement('quantity', $item->quantity);
                $stock->increment('in_transit_qty', $item->quantity);
                $this->createLedgerEntry($item->product_id, $order->warehouse_id, null, 'ship', -$item->quantity, $stock->fresh()->quantity, 'Order', $order->id);
            }
        }
    }

    /**
     * Deliver inventory.
     * in_transit_qty -= qty
     */
    public function deliver(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('in_transit_qty', $item->quantity);
                $this->createLedgerEntry($item->product_id, $order->warehouse_id, null, 'deliver', 0, $stock->fresh()->quantity, 'Order', $order->id);
            }
        }
    }

    /**
     * Handle return of a shipped order.
     * in_transit_qty -= qty, quantity += qty
     */
    public function handleReturn(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('in_transit_qty', $item->quantity);
                $stock->increment('quantity', $item->quantity);
                $this->createLedgerEntry($item->product_id, $order->warehouse_id, null, 'return', $item->quantity, $stock->fresh()->quantity, 'Order', $order->id, 'Shipment Returned');
            }
        }
    }

    /**
     * Restock inventory from a return.
     * quantity += qty
     */
    public function restock(int $productId, int $warehouseId, float $qty, string $referenceType, int $referenceId, ?int $batchId = null, ?string $notes = null): void
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0, 'reserved_qty' => 0, 'committed_qty' => 0, 'in_transit_qty' => 0]
        );
        $stock->lockForUpdate();
        $stock->increment('quantity', $qty);
        $this->createLedgerEntry($productId, $warehouseId, $batchId, 'return', $qty, $stock->fresh()->quantity, $referenceType, $referenceId, $notes);
    }

    /**
     * Manual stock adjustment.
     */
    public function adjust(int $productId, int $warehouseId, float $newQty, string $notes = ''): void
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0, 'reserved_qty' => 0, 'committed_qty' => 0, 'in_transit_qty' => 0]
        );
        $stock->lockForUpdate();
        $diff = $newQty - $stock->quantity;
        $stock->update(['quantity' => $newQty]);
        $this->createLedgerEntry($productId, $warehouseId, null, 'adjust', $diff, $newQty, 'Manual', 0, $notes);
    }

    /**
     * Receive inventory from a purchase order.
     * quantity += qty
     */
    public function purchaseReceive(int $productId, int $warehouseId, float $qty, int $orderId, ?int $batchId = null): void
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0, 'reserved_qty' => 0, 'committed_qty' => 0, 'in_transit_qty' => 0]
        );
        $stock->lockForUpdate();
        $stock->increment('quantity', $qty);
        if ($batchId) {
            StockBatch::where('id', $batchId)->increment('qty', $qty);
        }
        $this->createLedgerEntry($productId, $warehouseId, $batchId, 'purchase_receive', $qty, $stock->fresh()->quantity, 'Order', $orderId);
    }

    /**
     * Check available stock (on_hand - reserved - committed).
     */
    public function getAvailableQty(int $productId, int $warehouseId): float
    {
        $stock = Stock::where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
        if (!$stock) return 0;
        return max(0, $stock->quantity - $stock->reserved_qty - $stock->committed_qty);
    }

    private function createLedgerEntry(
        int $productId, int $warehouseId, ?int $batchId,
        string $type, float $quantity, float $balanceAfter,
        string $referenceType, int $referenceId, ?string $notes = null
    ): void {
        InventoryLedger::create([
            'product_id'     => $productId,
            'warehouse_id'   => $warehouseId,
            'batch_id'       => $batchId,
            'type'           => $type,
            'quantity'       => $quantity,
            'balance_after'  => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'notes'          => $notes,
            'created_by'     => Auth::id(),
        ]);
    }
}
