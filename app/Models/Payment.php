<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model {
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'payment_date' => 'date',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
}
