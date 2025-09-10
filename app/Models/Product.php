<?php

namespace App\Models;

use App\Enums\ProductOrganicType;
use App\Enums\ProductType;
use App\Http\Resources\ProductResource;
use App\Models\Highlight;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids,
        HasFactory,
        SoftDeletes,
        BroadcastsEvents;

    protected $hidden = ['profit_margin'];

    protected $casts = [
        'is_homemade' => 'boolean',
        'type' => ProductType::class,
        'organic_type' => ProductOrganicType::class,
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
        $this->loadMissing('ingredients');

        return (new ProductResource($this))->resolve();
    }

    /**
     * Relations
     */

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(Highlight::class)
            ->withPivot('quantity');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->using(IngredientProduct::class)
            ->withPivot(['is_showed', 'quantity', 'quantity_format'])
            ->withTimestamps();
    }

    public function availableIngredients()
    {
        $categories = $this->categories()->pluck('id');

        return Ingredient::whereHas('categories', function($query) use ($categories) {
            $query->whereIn('category_id', $categories);
        })->get();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }
}
