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
        return DB::transaction(function () use ($item, $pickedQty) {

            // ✅ Prevent invalid values
            $pickedQty = max(0, $pickedQty);

            // ✅ Prevent over-picking
            if ($pickedQty > $item->requested_qty) {
                $pickedQty = $item->requested_qty;
            }

            // ✅ Determine status safely
            $status = 'pending';
            if ($pickedQty == 0) {
                $status = 'pending';
            } elseif ($pickedQty < $item->requested_qty) {
                $status = 'partial';
            } else {
                $status = 'picked';
            }

            $item->update([
                'picked_qty' => $pickedQty,
                'status'     => $status,
                'picked_at'  => now(),
            ]);

            // ✅ Reload relation safely
            $pickList = $item->pickList()->with('items')->first();

            // ✅ More robust completion check
            $allPicked = $pickList->items
                ->every(function ($i) {
                    return in_array($i->status, ['picked', 'skipped']);
                });

            if ($allPicked && $pickList->status !== 'completed') {
                $pickList->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);
            }

            return $item->fresh();
        });
    }

    /**
     * Start picking — update pick list to in_progress.
     */
    public function startPickList(PickList $pickList): PickList
    {
        // ✅ Avoid overwriting if already started/completed
        if (!in_array($pickList->status, ['pending'])) {
            return $pickList;
        }

        $pickList->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);

        return $pickList->fresh();
    }
}