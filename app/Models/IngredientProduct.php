<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientProduct extends Pivot
{
    use BroadcastsEvents;

    protected $casts = [
        'quantity' => 'integer',
        'is_showed' => 'boolean',
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
}
