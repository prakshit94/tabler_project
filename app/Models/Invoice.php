<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function party() {
        return $this->belongsTo(Party::class);
    }
    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }
    public function payments() {
        return $this->hasMany(Payment::class);
    }
}
