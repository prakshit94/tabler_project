<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TallySyncLog extends Model
{
    protected $guarded = [];
    protected $casts = [
        'last_attempt_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isFailed(): bool { return $this->status === 'failed'; }
    public function isSuccess(): bool { return $this->status === 'success'; }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeFailed($query) { return $query->where('status', 'failed'); }
    public function scopeRetryable($query, int $maxRetries = 3)
    {
        return $query->where('status', 'failed')->where('retry_count', '<', $maxRetries);
    }
}
