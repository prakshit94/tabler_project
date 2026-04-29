<?php
namespace App\Services;

use App\Models\Order;
use App\Models\PickList;
use App\Models\PickListItem;
use App\Models\OrderAllocation;
use Illuminate\Support\Facades\DB;

class PickingService
{
    /**
     * Generate a pick list from order allocations.
     */
    public function generatePickList(Order $order): PickList
    {
        return DB::transaction(function () use ($order) {
            $pickList = PickList::create([
                'pick_list_number' => 'PL-' . strtoupper(uniqid()),
                'order_id'         => $order->id,
                'warehouse_id'     => $order->warehouse_id,
                'status'           => 'pending',
            ]);

            $allocations = OrderAllocation::where('order_id', $order->id)
                ->where('status', 'allocated')
                ->get();

            foreach ($allocations as $allocation) {
                PickListItem::create([
                    'pick_list_id'  => $pickList->id,
                    'order_item_id' => $allocation->order_item_id,
                    'product_id'    => $allocation->product_id,
                    'batch_id'      => $allocation->batch_id,
                    'bin_location'  => $allocation->bin_location,
                    'requested_qty' => $allocation->allocated_qty,
                    'picked_qty'    => 0,
                    'status'        => 'pending',
                ]);
            }

            return $pickList;
        });
    }

    /**
     * Record a pick for a pick list item.
     */
    public function recordPick(PickListItem $item, float $pickedQty): PickListItem
    {
        $item->update([
            'picked_qty' => $pickedQty,
            'status'     => $pickedQty >= $item->requested_qty ? 'picked' : 'partial',
            'picked_at'  => now(),
        ]);

        // Check if whole pick list is done
        $pickList = $item->pickList;
        $allPicked = $pickList->items()->whereNotIn('status', ['picked', 'skipped'])->doesntExist();
        if ($allPicked) {
            $pickList->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
        }

        return $item->fresh();
    }

    /**
     * Start picking — update pick list to in_progress.
     */
    public function startPickList(PickList $pickList): PickList
    {
        $pickList->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);
        return $pickList->fresh();
    }
}
