<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_name', 'pincode', 'post_so_name', 'taluka_name', 'district_name', 'state_name'
    ];
}
