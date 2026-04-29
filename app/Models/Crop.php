<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'category', 'is_active'];

    public function parties()
    {
        return $this->belongsToMany(Party::class);
    }
}
