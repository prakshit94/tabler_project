<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Party extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'mobile', 'gstin', 'email'])
            ->setDescriptionForEvent(fn(string $eventName) => "Party {$eventName}")
            ->logOnlyDirty();
    }

    public function crops_list()
    {
        return $this->belongsToMany(Crop::class);
    }

    protected $fillable = [
        'uuid', 'party_code', 'name', 'first_name', 'middle_name', 'last_name', 'mobile', 'email',
        'phone_number_2', 'relative_phone', 'source', 'referred_by', 'type', 'category',
        'company_name', 'gstin', 'pan_number', 'land_area', 'land_unit', 'crops',
        'irrigation_type', 'credit_limit', 'outstanding_balance', 'opening_balance',
        'credit_valid_till', 'payment_terms', 'ledger_group', 'bank_name', 'account_number',
        'ifsc_code', 'branch_name', 'aadhaar_last4', 'kyc_completed', 'kyc_verified_at',
        'first_purchase_at', 'last_purchase_at', 'orders_count', 'is_active', 'is_blacklisted',
        'internal_notes', 'tags', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'crops' => 'array',
        'tags' => 'array',
        'kyc_completed' => 'boolean',
        'is_active' => 'boolean',
        'is_blacklisted' => 'boolean',
        'kyc_verified_at' => 'datetime',
        'first_purchase_at' => 'date',
        'last_purchase_at' => 'date',
        'credit_valid_till' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($party) {
            if (empty($party->uuid)) {
                $party->uuid = (string) Str::uuid();
            }
            if (empty($party->party_code)) {
                $prefix = strtoupper(substr($party->type, 0, 1)) === 'V' ? 'VEND-' : 'CUST-';
                $lastId = static::withTrashed()->max('id') ?? 0;
                $party->party_code = $prefix . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
            }
            if (empty($party->name) && ($party->first_name || $party->middle_name || $party->last_name)) {
                $party->name = trim(preg_replace('/\s+/', ' ', $party->first_name . ' ' . $party->middle_name . ' ' . $party->last_name));
            }
        });
    }

    public function addresses()
    {
        return $this->hasMany(PartyAddress::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(PartyAddress::class)->whereIn('type', ['billing', 'both'])->where('is_default', true);
    }

    public function shippingAddress()
    {
        return $this->hasOne(PartyAddress::class)->whereIn('type', ['shipping', 'both'])->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
