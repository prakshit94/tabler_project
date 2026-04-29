<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'party_id', 'type', 'label', 'contact_name', 'contact_phone',
        'address', 'address_line1', 'address_line2', 'village', 'taluka',
        'district', 'state', 'country', 'pincode', 'post_office',
        'latitude', 'longitude', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
