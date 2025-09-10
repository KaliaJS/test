<?php
// app/Rules/ValidRemovableIngredient.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Ingredient;

class ValidRemovableIngredient implements Rule
{
    protected $products;
    protected $value;
    protected $message;

    /**
     * @param \Illuminate\Database\Eloquent\Collection $products  Collection de Products préchargés
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Valide chaque ID d'ingrédient à retirer.
     */
    public function passes($attribute, $value): bool
    {
        $this->value = $value;

        // Récupération de l'index du produit dans l'input via l'attribut
        $parts = explode('.', $attribute);
        $index = $parts[1] ?? null;
        $productId = request()->input("products.{$index}.id");

        if (!$productId || ! $this->products->has($productId)) {
            // Le produit sera validé ailleurs (exists), on skip ici
            return true;
        }

        $product = $this->products->get($productId);
        $ingredient = $product->ingredients->firstWhere('id', $value);

        if (! $ingredient) {
            $this->message = "L'ingrédient ID {$value} ne fait pas partie des ingrédients standards du produit '{$product->name}'.";
            return false;
        }

        if (! $ingredient->is_removable) {
            $this->message = "L'ingrédient '{$ingredient->name}' (ID {$value}) ne peut pas être retiré du produit '{$product->name}'.";
            return false;
        }

        return true;
    }

    /**
     * Message d'erreur retourné en cas d'échec.
     */
    public function message(): string
    {
        return $this->message ?? "L'ingrédient ID {$this->value} est invalide.";
    }
}
