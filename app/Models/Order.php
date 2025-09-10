<?php

namespace App\Models;

use App\Http\Resources\OrderResource;
use App\Observers\OrderObserver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    use HasUuids,
        HasFactory,
        BroadcastsEvents;

    /**
     * Broadcasts
     */

    public function broadcastOn(): array
    {
        return array_filter([
            $this->user_id ? new PrivateChannel("App.Models.User.{$this->user_id}") : null,
            $this->guest_id ? new PrivateChannel("Guest.{$this->guest_id}") : null,
            new PrivateChannel('adminChannel'),
            new PrivateChannel('terminalChannel'),
        ]);
    }

    public function broadcastWith(): array
    {
        $this->loadMissing('products.modifications', 'products.product');

        return (new OrderResource($this))->resolve();
    }

    /**
     * Relations
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function rewardPoints(): HasOne
    {
        return $this->hasOne(RewardPoint::class);
    }

    public function orderMaker(): BelongsToMany
    {
        return $this->belongsToMany(OrderMaker::class, 'order_maker_orders')
            ->withTimestamps()
            ->withPivot('id');
    }

    /**
     * Scopes
     */

    public function scopeWithRelations($query)
    {
        return $query->with('products.modifications', 'products.product');
    }

    /**
     * Helpers
     */

    public function getNetRewardPointsAttribute(): int
    {
        return $this->rewardPoints()->sum('points');
    }

    public function getCurrentOrderMaker()
    {
        return $this->orderMaker()->first();
    }
}
