<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPoint extends Model
{
    use HasFactory;

    protected $casts = [
        'expires_at' => 'datetime',
        'is_expired' => 'boolean',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_expired', false)
                    ->where('expires_at', '>', now());
    }
    
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('is_expired', true)
              ->orWhere('expires_at', '<=', now());
        });
    }
}
