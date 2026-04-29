<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model {
    use SoftDeletes, LogsActivity;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'selling_price', 'purchase_price', 'is_active'])
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}")
            ->logOnlyDirty();
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function taxRate() {
        return $this->belongsTo(TaxRate::class);
    }
    public function hsnCode() {
        return $this->belongsTo(HsnCode::class);
    }
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function subCategory() {
        return $this->belongsTo(SubCategory::class);
    }
    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }
    public function prices() {
        return $this->hasMany(ProductPrice::class);
    }
    public function stocks() {
        return $this->hasMany(Stock::class);
    }
    public function defaultWarehouse() {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }
    public function images() {
        return $this->hasMany(ProductImage::class);
    }
}
