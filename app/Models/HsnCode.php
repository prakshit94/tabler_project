<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HsnCode extends Model {
    use SoftDeletes;
    protected $guarded = [];
    
    public function taxRate() {
        return $this->belongsTo(TaxRate::class);
    }
}
