<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model {
    use SoftDeletes;
    protected $guarded = [];

    public function addresses() {
        return $this->hasMany(PartyAddress::class);
    }
}
