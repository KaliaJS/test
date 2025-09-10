<?php

namespace App\Models;

use App\Models\OrderProductModification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderProduct extends Model
{
    use HasUuids,
        BroadcastsEvents;

    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'total_price' => 'integer',
        'is_done' => 'boolean',
    ];

    public function broadcastOn(): array
    {
        return array_filter([
            $this->user_id ? new PrivateChannel("App.Models.User.{$this->user_id}") : null,
            $this->guest_id ? new PrivateChannel("Guest.{$this->guest_id}") : null,
            new PrivateChannel('adminChannel'),
            new PrivateChannel('terminalChannel'),
        ]);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function modifications(): HasMany
     {
        return $this->hasMany(OrderProductModification::class);
     }

    /**
     * Helpers
     */
    public function hasModifications(): bool
    {
        return $this->type === 'customized';
    }

    public function getRemovedIngredients()
    {
        return $this->modifications()->where('action', 'remove')->get();
    }

    public function getExtraIngredients()
    {
        return $this->modifications()->where('action', 'extra')->get();
    }

    /**
      * Attributs calculés
      */
     public function getFormattedNameAttribute(): string
     {
         if ($this->type === 'standard') {
             return $this->name;
         }

         $parts = [$this->name];

         $removed = $this->getRemovedIngredients();
         if ($removed->isNotEmpty()) {
             $removedNames = $removed->pluck('ingredient_name')->join(', ');
             $parts[] = "sans {$removedNames}";
         }

         $extras = $this->getExtraIngredients();
         if ($extras->isNotEmpty()) {
             $extraParts = $extras->map(function ($mod) {
                 return $mod->ingredient_name . ' x' . ($mod->quantity + 1);
             })->join(', ');
             $parts[] = "avec {$extraParts}";
         }

         return implode(' - ', $parts);
     }
}
