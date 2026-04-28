<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model {
    protected $guarded = [];
    protected $casts = [
        'entry_date' => 'date',
    ];

    public function ledger() {
        return $this->belongsTo(Ledger::class);
    }
    public function party() {
        return $this->belongsTo(Party::class);
    }
}
