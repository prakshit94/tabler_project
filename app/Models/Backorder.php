<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backorder extends Model
{
    protected $guarded = [];
    protected $casts = [
        'expected_date' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }

    public function getRemainingQtyAttribute(): float
    {
        return max(0, $this->pending_qty - $this->fulfilled_qty);
    }
}
