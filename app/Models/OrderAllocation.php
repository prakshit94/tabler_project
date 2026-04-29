<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAllocation extends Model
{
    protected $guarded = [];

    public function order() { return $this->belongsTo(Order::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function batch() { return $this->belongsTo(StockBatch::class, 'batch_id'); }
}
