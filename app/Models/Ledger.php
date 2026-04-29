<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model {
    use SoftDeletes;
    protected $guarded = [];

    public function entries() {
        return $this->hasMany(AccountingEntry::class);
    }
}
