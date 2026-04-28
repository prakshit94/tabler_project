<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyAddress extends Model {
    protected $guarded = [];

    public function party() {
        return $this->belongsTo(Party::class);
    }
}
