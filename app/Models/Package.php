<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];
    protected $casts = ['packed_at' => 'datetime'];

    public function order() { return $this->belongsTo(Order::class); }
    public function items() { return $this->hasMany(PackageItem::class); }
}
