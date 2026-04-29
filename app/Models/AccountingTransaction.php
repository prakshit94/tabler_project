<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingTransaction extends Model
{
    protected $guarded = [];
    protected $casts = ['transaction_date' => 'date'];

    public function entries() { return $this->hasMany(AccountingEntry::class, 'transaction_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function getTotalDebitAttribute(): float
    {
        return $this->entries->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->entries->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }
}
