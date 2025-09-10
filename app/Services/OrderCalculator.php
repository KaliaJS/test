<?php

namespace App\Services;

use App\Models\Product;

class OrderCalculator
{
    public function calculateUnitPrice(Product $product, array $modifications): int
    {
        $basePrice = $product->price;
        $supplementsTotal = $this->calculateSupplementsPrice($product, $modifications);
        
        return $basePrice + $supplementsTotal;
    }

    private function calculateSupplementsPrice(Product $product, array $modifications): int
    {
        return collect($modifications)
            ->filter(fn($mod) => $mod['action'] === 'extra')
            ->sum(function ($mod) use ($product) {
                $ingredient = $product->ingredients->find($mod['ingredient_id']);
                return $ingredient?->supplement_price 
                    ? $ingredient->supplement_price * $mod['quantity']
                    : 0;
            });
    }
}
