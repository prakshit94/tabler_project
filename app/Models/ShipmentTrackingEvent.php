<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentTrackingEvent extends Model
{
    protected $guarded = [];
    protected $casts = ['event_at' => 'datetime'];

    public function shipment() { return $this->belongsTo(Shipment::class); }
}
