<?php

namespace App\Events;

use App\Http\Resources\SensorMeasurementResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SensorMeasurementsCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Collection $measurements
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('adminChannel');
    }

    public function broadcastWith(): array
    {
        return SensorMeasurementResource::collection($this->measurements)->resolve();
    }

    public function broadcastAs(): string
    {
        return 'SensorMeasurementsCreated';
    }
}
