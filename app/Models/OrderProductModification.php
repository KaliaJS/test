<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProductModification extends Model
{
    use HasFactory;

    protected $casts = [
        'action' => 'string',
        'quantity' => 'integer',
        'supplement_price' => 'integer',
    ];

    /**
     * Relations
     */
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Scopes
     */
    public function scopeRemovals($query)
    {
        return $query->where('action', 'remove');
    }

    public function scopeExtras($query)
    {
        return $query->where('action', 'extra');
    }

    /**
     * Helpers
     */
    public function isRemoval(): bool
    {
        return $this->action === 'remove';
    }

    public function isExtra(): bool
    {
        return $this->action === 'extra';
    }
}

