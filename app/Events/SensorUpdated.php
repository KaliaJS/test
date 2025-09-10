<?php

namespace App\Events;

use App\Http\Resources\SensorResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SensorUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Collection $sensors
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('adminChannel');
    }

    public function broadcastWith(): array
    {
        return SensorResource::collection($this->sensors)->resolve();
    }

    public function broadcastAs(): string
    {
        return 'SensorUpdated';
    }
}
