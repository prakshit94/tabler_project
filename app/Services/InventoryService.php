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
    public function reserve(int $productId, int $warehouseId, float $qty, int $orderId): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->increment('reserved_qty', $qty);
            $this->createLedgerEntry($productId, $warehouseId, null, 'reserve', $qty, $stock->fresh()->quantity, 'Order', $orderId);
        }
    }

    /**
     * Release reserved inventory (e.g. order cancelled).
     * reserved_qty -= qty
     */
    public function release(int $productId, int $warehouseId, float $qty, int $orderId): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->decrement('reserved_qty', $qty);
            $this->createLedgerEntry($productId, $warehouseId, null, 'release', -$qty, $stock->fresh()->quantity, 'Order', $orderId);
        }
    }

    /**
     * Commit inventory when items are picked.
     * reserved_qty -= qty, committed_qty += qty
     */
    public function commit(int $productId, int $warehouseId, float $qty, int $orderId): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->decrement('reserved_qty', $qty);
            $stock->increment('committed_qty', $qty);
            $this->createLedgerEntry($productId, $warehouseId, null, 'commit', $qty, $stock->fresh()->quantity, 'Order', $orderId);
        }
    }

    /**
     * Ship inventory.
     * committed_qty -= qty, quantity -= qty, in_transit_qty += qty
     */
    public function ship(int $productId, int $warehouseId, float $qty, int $orderId): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->decrement('committed_qty', $qty);
            $stock->decrement('quantity', $qty);
            $stock->increment('in_transit_qty', $qty);
            $this->createLedgerEntry($productId, $warehouseId, null, 'ship', -$qty, $stock->fresh()->quantity, 'Order', $orderId);
        }
    }

    /**
     * Deliver inventory.
     * in_transit_qty -= qty
     */
    public function deliver(int $productId, int $warehouseId, float $qty, int $orderId): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->decrement('in_transit_qty', $qty);
            $this->createLedgerEntry($productId, $warehouseId, null, 'deliver', 0, $stock->fresh()->quantity, 'Order', $orderId);
        }
    }

    /**
     * Handle return of a shipped order.
     * in_transit_qty -= qty, quantity += qty
     */
    public function handleReturn(int $productId, int $warehouseId, float $qty, int $orderId): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->decrement('in_transit_qty', $qty);
            $stock->increment('quantity', $qty);
            $this->createLedgerEntry($productId, $warehouseId, null, 'return', $qty, $stock->fresh()->quantity, 'Order', $orderId, 'Shipment Returned');
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
