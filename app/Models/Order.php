<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model {
    use SoftDeletes, LogsActivity;
    protected $guarded = [];

    // Valid statuses for display
    const STATUSES = [
        'draft', 'confirmed', 'allocated', 'picking', 'picked',
        'packing', 'packed', 'shipped', 'in_transit', 'delivered', 'closed',
        'on_hold', 'cancelled', 'backordered', 'partial', 'return_initiated', 'return_completed',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_number', 'status', 'total_amount', 'type'])
            ->setDescriptionForEvent(fn(string $eventName) => "Order {$eventName}")
            ->logOnlyDirty();
    }

    protected $casts = [
        'order_date'    => 'date',
        'confirmed_at'  => 'datetime',
        'allocated_at'  => 'datetime',
        'picking_at'    => 'datetime',
        'picked_at'     => 'datetime',
        'packing_at'    => 'datetime',
        'packed_at'     => 'datetime',
        'shipped_at'    => 'datetime',
        'delivered_at'  => 'datetime',
        'closed_at'     => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    // --- Existing relationships ---
    public function party() { return $this->belongsTo(Party::class); }
    public function shippingAddress() { return $this->belongsTo(PartyAddress::class, 'shipping_address_id'); }
    public function billingAddress() { return $this->belongsTo(PartyAddress::class, 'billing_address_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    // --- WMS relationships ---
    public function allocations() { return $this->hasMany(OrderAllocation::class); }
    public function pickLists() { return $this->hasMany(PickList::class); }
    public function packages() { return $this->hasMany(Package::class); }
    public function shipments() { return $this->hasMany(Shipment::class); }
    public function backorders() { return $this->hasMany(Backorder::class); }
    public function returns() { return $this->hasMany(OrderReturn::class); }

    // --- Helper methods ---
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'confirmed', 'on_hold']);
    }

    public function canTransitionTo(string $status): bool
    {
        $allowed = \App\Services\OrderService::TRANSITIONS[$this->status] ?? [];
        return in_array($status, $allowed);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft'            => 'bg-secondary-lt text-secondary',
            'confirmed'        => 'bg-blue-lt text-blue',
            'allocated'        => 'bg-cyan-lt text-cyan',
            'picking'          => 'bg-yellow-lt text-yellow',
            'picked'           => 'bg-lime-lt text-lime',
            'packing'          => 'bg-orange-lt text-orange',
            'packed'           => 'bg-teal-lt text-teal',
            'shipped'          => 'bg-purple-lt text-purple',
            'in_transit'       => 'bg-indigo-lt text-indigo',
            'delivered'        => 'bg-green-lt text-green',
            'closed'           => 'bg-dark-lt text-dark',
            'cancelled'        => 'bg-red-lt text-red',
            'on_hold'          => 'bg-muted-lt text-muted',
            'backordered'      => 'bg-warning-lt text-warning',
            'return_initiated' => 'bg-pink-lt text-pink',
            'return_completed' => 'bg-success-lt text-success',
            default            => 'bg-secondary-lt text-secondary',
        };
    }
}

