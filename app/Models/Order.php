<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model {
    use SoftDeletes, LogsActivity;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_number', 'status', 'total_amount', 'type'])
            ->setDescriptionForEvent(fn(string $eventName) => "Order {$eventName}")
            ->logOnlyDirty();
    }
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
