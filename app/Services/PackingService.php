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
        return Package::create([
            'package_number' => 'PKG-' . strtoupper(uniqid()),
            'order_id'       => $order->id,
            'weight'         => $data['weight'] ?? null,
            'dimensions'     => $data['dimensions'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'status'         => 'packing',
        ]);
    }

    /**
     * Add item to a package.
     */
    public function addItem(Package $package, int $orderItemId, int $productId, float $qty): PackageItem
    {
        return PackageItem::create([
            'package_id'    => $package->id,
            'order_item_id' => $orderItemId,
            'product_id'    => $productId,
            'quantity'      => $qty,
        ]);
    }

    /**
     * Seal a package — mark it packed.
     */
    public function sealPackage(Package $package): Package
    {
        $package->update([
            'status'    => 'packed',
            'packed_at' => now(),
        ]);

        // Check if all order packages are packed
        $order = $package->order;
        $allPacked = $order->packages()->where('status', '!=', 'packed')->doesntExist();
        if ($allPacked && $order->packages()->count() > 0) {
            // Caller (PackingController) can then call OrderService::markPacked()
        }

        return $package->fresh();
    }

    /**
     * Get packing summary for an order.
     */
    public function getSummary(Order $order): array
    {
        $packages = $order->packages()->with('items.product')->get();
        $totalItems = $order->items->sum('quantity');
        $packedItems = $packages->sum(fn ($pkg) => $pkg->items->sum('quantity'));

        return [
            'packages'     => $packages,
            'total_items'  => $totalItems,
            'packed_items' => $packedItems,
            'is_complete'  => $totalItems > 0 && $packedItems >= $totalItems,
        ];
    }
}
