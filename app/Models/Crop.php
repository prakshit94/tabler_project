<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'category', 'is_active'];

    public function parties()
    {
        return $this->belongsToMany(Party::class);
    }
}
