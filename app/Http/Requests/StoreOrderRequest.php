<?php
// app/Http/Requests/StoreOrderRequest.php

namespace App\Http\Requests;

use App\Models\Product;
use App\Rules\ValidRemovableIngredient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    private $productsCache = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.product_id' => 'required|uuid',
            'products.*.quantity' => 'required|integer|min:1|max:10',
            'products.*.modifications' => 'present|array',
            'products.*.modifications.*.ingredient_id' => 'required|uuid',
            'products.*.modifications.*.action' => 'required|in:extra,remove',
            'products.*.modifications.*.quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'products' => 'Le panier ne peut pas être vide.',
            'products.*.quantity.min' => 'La quantité minimale est de 1.',
            'products.*.quantity.max' => 'La quantité maximale est de 10.',
            'products.*.modifications.*.action.in' => 'L\'action doit être extra ou remove.',
        ];
    }

    /**
     * Validation personnalisée après les règles de base
     * Optimisée pour minimiser les requêtes
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $data = $this->all();
            
            // Vérifier qu'il y a au moins un item
            if (!is_array($data) || count($data) === 0) {
                $validator->errors()->add('cart', 'Le panier est vide.');
                return;
            }

            // Charger tous les produits nécessaires en UNE SEULE requête
            $productIds = collect($data)
                ->pluck('product_id')
                ->filter(fn($id) => Str::isUuid($id))
                ->unique()
                ->filter();
            if ($productIds->isEmpty()) return;
            
            $this->productsCache = Product::with('ingredients')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            // Valider chaque item
            foreach ($data as $index => $item) {
                if (!isset($item['product_id'])) continue;
                
                $product = $this->productsCache->get($item['product_id']);
                if (!$product) {
                    $validator->errors()->add(
                        "products.{$index}.product_id",
                        "Le produit sélectionné n'existe pas."
                    );
                    continue;
                }
                
                // Valider chaque modification
                if (isset($item['modifications']) && is_array($item['modifications'])) {
                    $this->validateModifications($validator, $index, $item['modifications'], $product);
                }
            }
        });
    }

    /**
     * Valide les modifications d'un produit
     */
    private function validateModifications(
        Validator $validator, 
        int $index, 
        array $modifications, 
        Product $product
    ): void {
        $productIngredients = $product->ingredients->keyBy('id');
        
        foreach ($modifications as $modIndex => $mod) {
            if (!isset($mod['ingredient_id'])) continue;
            
            $ingredient = $productIngredients->get($mod['ingredient_id']);
            
            // Vérifier que l'ingrédient appartient au produit
            if (!$ingredient) {
                $validator->errors()->add(
                    "{$index}.modifications.{$modIndex}",
                    "L'ingrédient n'appartient pas à ce produit."
                );
                continue;
            }

            // Valider selon l'action
            if ($mod['action'] === 'remove') {
                $this->validateRemoveAction($validator, $index, $modIndex, $mod, $ingredient);
            } elseif ($mod['action'] === 'extra') {
                $this->validateExtraAction($validator, $index, $modIndex, $mod, $ingredient);
            }
        }
    }

    /**
     * Valide une action de suppression
     */
    private function validateRemoveAction(
        Validator $validator,
        int $index,
        int $modIndex,
        array $mod,
        $ingredient
    ): void {
        if (!$ingredient->is_removable) {
            $validator->errors()->add(
                "{$index}.modifications.{$modIndex}",
                "L'ingrédient '{$ingredient->name}' ne peut pas être supprimé."
            );
        }
        
        if ($mod['quantity'] !== 0) {
            $validator->errors()->add(
                "{$index}.modifications.{$modIndex}.quantity",
                "La quantité doit être 0 pour une suppression."
            );
        }
    }

    /**
     * Valide une action d'ajout extra
     */
    private function validateExtraAction(
        Validator $validator,
        int $index,
        int $modIndex,
        array $mod,
        $ingredient
    ): void {
        if (!$ingredient->supplement_price || $ingredient->supplement_price <= 0) {
            $validator->errors()->add(
                "{$index}.modifications.{$modIndex}",
                "L'ingrédient '{$ingredient->name}' ne peut pas avoir de supplément."
            );
            return;
        }
        
        if ($mod['quantity'] < 1) {
            $validator->errors()->add(
                "{$index}.modifications.{$modIndex}.quantity",
                "La quantité d'extra doit être au moins 1."
            );
        }
        
        if ($ingredient->max_supplement && $mod['quantity'] > $ingredient->max_supplement) {
            $validator->errors()->add(
                "{$index}.modifications.{$modIndex}.quantity",
                "Maximum {$ingredient->max_supplement} supplément(s) pour '{$ingredient->name}'."
            );
        }
    }
}
