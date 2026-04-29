<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickList extends Model
{
    protected $guarded = [];
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function items() { return $this->hasMany(PickListItem::class); }

    public function isComplete(): bool
    {
        return $this->items()->where('status', '!=', 'picked')->doesntExist();
    }
}
