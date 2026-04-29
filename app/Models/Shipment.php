<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $guarded = [];
    protected $casts = [
        'estimated_delivery' => 'date',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function trackingEvents() { return $this->hasMany(ShipmentTrackingEvent::class)->orderByDesc('event_at'); }
    public function latestEvent() { return $this->hasOne(ShipmentTrackingEvent::class)->latestOfMany('event_at'); }
}
