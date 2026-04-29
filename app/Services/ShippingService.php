<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentTrackingEvent;
use App\Events\OrderShipped;
use Illuminate\Support\Facades\DB;

class ShippingService
{
    /**
     * Create a shipment for an order.
     */
    public function createShipment(Order $order, array $data): Shipment
    {
        return DB::transaction(function () use ($order, $data) {
            $shipment = Shipment::create([
                'shipment_number'    => 'SHP-' . strtoupper(uniqid()),
                'order_id'           => $order->id,
                'carrier'            => $data['carrier'] ?? null,
                'tracking_number'    => $data['tracking_number'] ?? null,
                'tracking_url'       => $data['tracking_url'] ?? null,
                'status'             => 'dispatched',
                'estimated_delivery' => $data['estimated_delivery'] ?? null,
                'shipping_cost'      => $data['shipping_cost'] ?? 0,
                'notes'              => $data['notes'] ?? null,
                'shipped_at'         => now(),
            ]);

            $this->addTrackingEvent($shipment, 'dispatched', $data['origin'] ?? 'Warehouse', 'Shipment dispatched');

            return $shipment;
        });
    }

    /**
     * Add a tracking event to a shipment.
     */
    public function addTrackingEvent(Shipment $shipment, string $status, ?string $location, ?string $description = null): ShipmentTrackingEvent
    {
        $event = ShipmentTrackingEvent::create([
            'shipment_id' => $shipment->id,
            'status'      => $status,
            'location'    => $location,
            'description' => $description,
            'event_at'    => now(),
        ]);

        $shipment->update(['status' => $status]);

        return $event;
    }

    /**
     * Mark shipment as delivered.
     */
    public function markDelivered(Shipment $shipment): Shipment
    {
        $shipment->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
        $this->addTrackingEvent($shipment, 'delivered', null, 'Package delivered successfully');
        return $shipment->fresh();
    }

    /**
     * Get all active shipments with tracking.
     */
    public function getActiveShipments()
    {
        return Shipment::with(['order.party', 'latestEvent'])
            ->whereNotIn('status', ['delivered', 'returned'])
            ->latest()
            ->paginate(20);
    }
}
