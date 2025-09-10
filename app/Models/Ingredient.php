<?php

namespace App\Models;

use App\Enums\ProductOrganicType;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasUuids,
        HasFactory;

    protected $casts = [
        'organic_type' => ProductOrganicType::class,
        'is_removable' => 'boolean',
        'is_swiss' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_ingredient');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ingredient_product');
    }

    public function removedFromOrderProducts(): BelongsToMany
    {
        return $this->belongsToMany(OrderProduct::class, 'order_product_removed_ingredients');
    }
}
