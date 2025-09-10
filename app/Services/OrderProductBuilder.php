<?php

namespace App\Services;

use App\Models\Product;
use App\Services\OrderCalculator;
use Illuminate\Support\Collection;

class OrderProductBuilder
{
    public function __construct(
        protected OrderCalculator $calculator
    ) {}

    public function buildOrderData(array $items, Collection $products): array
    {
        $result = [
            'products' => [],
            'modifications' => [],
            'totalAmount' => 0,
            'totalManufacturingTime' => 0,
        ];

        foreach ($items as $index => $item) {
            $product = $products[$item['product_id']];
            
            $productData = $this->buildProductData($item, $product);
            $result['products'][] = $productData['product'];
            $result['modifications'][$index] = $productData['modifications'];
            
            $result['totalAmount'] += $productData['product']['total_price'];
            $result['totalManufacturingTime'] += ($product->manufacturing_time ?? 0) * $item['quantity'];
        }

        return $result;
    }

    private function buildProductData(array $item, Product $product): array
    {
        $unitPrice = $this->calculator->calculateUnitPrice($product, $item['modifications'] ?? []);
        $totalPrice = $unitPrice * $item['quantity'];

        return [
            'product' => [
                'product_id' => $item['product_id'],
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'is_done' => false,
            ],
            'modifications' => $this->buildModifications($item['modifications'] ?? [], $product),
        ];
    }

    private function buildModifications(array $modifications, Product $product): array
    {
        return collect($modifications)
            ->map(function ($mod) use ($product) {
                $ingredient = $product->ingredients->find($mod['ingredient_id']);
                
                if (!$ingredient) {
                    return null;
                }

                return [
                    'ingredient_id' => $mod['ingredient_id'],
                    'ingredient_name' => $ingredient->name,
                    'action' => $mod['action'],
                    'quantity' => $mod['quantity'],
                    'supplement_price' => $mod['action'] === 'extra' 
                        ? ($ingredient->supplement_price * $mod['quantity']) 
                        : 0,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
