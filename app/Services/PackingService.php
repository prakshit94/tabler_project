<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Support\Facades\DB;

class PackingService
{
    /**
     * Create a new package for an order.
     */
    public function createPackage(Order $order, array $data): Package
    {
        return DB::transaction(function () use ($order, $data) {
            
            // ✅ Auto-transition to packing if currently picked
            if ($order->status === 'picked') {
                app(\App\Services\OrderService::class)->startPacking($order);
            }

            return Package::create([
                'package_number' => 'PKG-' . strtoupper(uniqid()),
                'order_id'       => $order->id,
                'weight'         => $data['weight'] ?? null,
                'dimensions'     => $data['dimensions'] ?? null,
                'notes'          => $data['notes'] ?? null,
                'status'         => 'packing',
            ]);
        });
    }

    /**
     * Add item to a package.
     */
    public function addItem(Package $package, int $orderItemId, int $productId, float $qty): PackageItem
    {
        return DB::transaction(function () use ($package, $orderItemId, $productId, $qty) {

            // ✅ Prevent invalid quantity
            $qty = max(0, $qty);
            if ($qty == 0) {
                throw new \InvalidArgumentException('Quantity must be greater than 0');
            }

            // ✅ Prevent adding items to already packed package
            if ($package->status === 'packed') {
                throw new \Exception('Cannot add items to a sealed package');
            }

            // ✅ Prevent over-packing (validate against order item quantity)
            $orderItem = \App\Models\OrderItem::findOrFail($orderItemId);
            $totalPacked = PackageItem::whereHas('package', function($q) use ($package) {
                $q->where('order_id', $package->order_id);
            })->where('order_item_id', $orderItemId)->sum('quantity');

            if (($totalPacked + $qty) > $orderItem->quantity) {
                throw new \InvalidArgumentException('Cannot over-pack item. Total requested: ' . $orderItem->quantity);
            }

            // ✅ Merge if same item already exists
            $existing = PackageItem::where([
                'package_id'    => $package->id,
                'order_item_id' => $orderItemId,
                'product_id'    => $productId,
            ])->first();

            if ($existing) {
                $existing->increment('quantity', $qty);
                return $existing->fresh();
            }

            return PackageItem::create([
                'package_id'    => $package->id,
                'order_item_id' => $orderItemId,
                'product_id'    => $productId,
                'quantity'      => $qty,
            ]);
        });
    }

    /**
     * Seal a package — mark it packed.
     */
    public function sealPackage(Package $package): Package
    {
        return DB::transaction(function () use ($package) {

            // ✅ Prevent resealing
            if ($package->status === 'packed') {
                return $package;
            }

            $package->update([
                'status'    => 'packed',
                'packed_at' => now(),
            ]);

            // ✅ Reload order with packages
            $order = $package->order()->with('packages')->first();

            $allPacked = $order->packages
                ->every(fn ($pkg) => $pkg->status === 'packed');

            if ($allPacked && $order->packages->count() > 0) {
                // Intentionally left as-is (your design)
                // Controller/Service will handle Order status update
            }

            return $package->fresh();
        });
    }

    /**
     * Get packing summary for an order.
     */
    public function getSummary(Order $order): array
    {
        $packages = $order->packages()->with('items.product')->get();

        $totalItems = $order->items->sum('quantity');

        $packedItems = $packages->sum(function ($pkg) {
            return $pkg->items->sum('quantity');
        });

        // ✅ Account for backorders (partial fulfillment)
        $pendingBackorders = \App\Models\Backorder::where('order_id', $order->id)
            ->where('status', 'pending')
            ->sum('pending_qty');
        
        $expectedToPack = max(0, $totalItems - $pendingBackorders);

        return [
            'packages'     => $packages,
            'total_items'  => $totalItems,
            'packed_items' => $packedItems,

            // ✅ is_complete now handles partial shipments correctly
            'is_complete'  => $expectedToPack > 0 && $packedItems >= $expectedToPack,
        ];
    }
}