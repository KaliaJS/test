<?php

namespace App\Models;

use App\Http\Resources\HighlightResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Highlight extends Model
{
    use HasFactory,
        HasUuids,
        BroadcastsEvents;

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
        $this->loadMissing('products');

        return (new HighlightResource($this))->resolve();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity');
    }

    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->products->sum(function ($product) {
                return $product->price * $product->pivot->quantity;
            })
        );
    }
}