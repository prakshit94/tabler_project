<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'order_date' => 'date',
    ];

    public function party() {
        return $this->belongsTo(Party::class);
    }
    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function invoice() {
        return $this->hasOne(Invoice::class);
    }
    public function payments() {
        return $this->hasMany(Payment::class);
    }
}
