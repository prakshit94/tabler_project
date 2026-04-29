<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLedger extends Model
{
    protected $table = 'inventory_ledger';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime'];

    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function batch() { return $this->belongsTo(StockBatch::class, 'batch_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
