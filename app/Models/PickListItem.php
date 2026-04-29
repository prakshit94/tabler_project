<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickListItem extends Model
{
    protected $guarded = [];
    protected $casts = ['picked_at' => 'datetime'];

    public function pickList() { return $this->belongsTo(PickList::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function batch() { return $this->belongsTo(StockBatch::class, 'batch_id'); }
}
