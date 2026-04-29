<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderAllocation;
use App\Models\StockBatch;
use App\Models\SystemSetting;

class AllocationService
{
    /**
     * Allocate batches to order items using FIFO or FEFO.
     * Returns true if fully allocated, false if partial/backorder.
     */
    public function allocate(Order $order): bool
    {
        $method = SystemSetting::get('allocation_method', 'fifo');
        $fullyAllocated = true;

        foreach ($order->items as $item) {
            $remaining = $item->quantity;
            $batches = $this->getAvailableBatches($item->product_id, $order->warehouse_id, $method);

            foreach ($batches as $batch) {
                if ($remaining <= 0) break;

                $available = $batch->qty - $batch->reserved_qty;
                if ($available <= 0) continue;

                $toAllocate = min($remaining, $available);

                OrderAllocation::create([
                    'order_id'      => $order->id,
                    'order_item_id' => $item->id,
                    'product_id'    => $item->product_id,
                    'warehouse_id'  => $order->warehouse_id,
                    'batch_id'      => $batch->id,
                    'bin_location'  => $batch->bin_location,
                    'allocated_qty' => $toAllocate,
                    'status'        => 'allocated',
                ]);

                $batch->increment('reserved_qty', $toAllocate);
                $remaining -= $toAllocate;
            }

            if ($remaining > 0) {
                // No batch — allocate without batch
                $stock = \App\Models\Stock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $order->warehouse_id)->first();

                $available = $stock ? max(0, $stock->quantity - $stock->reserved_qty - $stock->committed_qty) : 0;
                $toAllocate = min($remaining, $available);

                if ($toAllocate > 0) {
                    OrderAllocation::create([
                        'order_id'      => $order->id,
                        'order_item_id' => $item->id,
                        'product_id'    => $item->product_id,
                        'warehouse_id'  => $order->warehouse_id,
                        'batch_id'      => null,
                        'allocated_qty' => $toAllocate,
                        'status'        => 'allocated',
                    ]);
                    $remaining -= $toAllocate;
                }

                if ($remaining > 0) {
                    $fullyAllocated = false;
                }
            }
        }

        return $fullyAllocated;
    }

    private function getAvailableBatches(int $productId, int $warehouseId, string $method)
    {
        $query = StockBatch::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereRaw('qty - reserved_qty > 0');

        if ($method === 'fefo') {
            $query->whereNotNull('expiry_date')->orderBy('expiry_date');
        } else {
            // FIFO — use manufacture date or created_at
            $query->orderBy('manufacture_date')->orderBy('id');
        }

        return $query->get();
    }
}
