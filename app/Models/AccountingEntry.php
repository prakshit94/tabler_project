<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    protected $guarded = [];
    protected $casts = ['entry_date' => 'date'];

    public function transaction() { return $this->belongsTo(AccountingTransaction::class, 'transaction_id'); }
    public function ledger() { return $this->belongsTo(Ledger::class); }
}
