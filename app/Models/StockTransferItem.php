<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model {
    protected $guarded = [];

    public function stockTransfer() {
        return $this->belongsTo(StockTransfer::class, 'transfer_id');
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
