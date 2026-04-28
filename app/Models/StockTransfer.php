<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model {
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fromWarehouse() {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse() {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function items() {
        return $this->hasMany(StockTransferItem::class, 'transfer_id');
    }
}
