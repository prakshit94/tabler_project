<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReturn extends Model {
    use SoftDeletes;
    protected $table = 'returns';
    protected $guarded = [];
    protected $casts = [
        'return_date' => 'date',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function party() {
        return $this->belongsTo(Party::class);
    }
    public function items() {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
