<?php

namespace App\Models;

use App\Http\Resources\TruckResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use HasUuids,
        HasFactory,
        BroadcastsEvents;

    protected $casts = [
        'is_ready' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $attributes = [
        'is_ready' => false,
    ];

    /**
     * Broadcasts
     */
    public function broadcastOn(string $event): Channel
    {
        return new Channel('public');
    }

    public function broadcastWith(): array
    {
        return (new TruckResource($this))->resolve();
    }

    /**
     * Relations
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
