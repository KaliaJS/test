<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'quantity' => $this->faker->numberBetween(1, 100),
            'organic_type' => $this->faker->numberBetween(1, 3),
            'is_swiss' => $this->faker->boolean,
        ];
    }

    public function withCategories($categoryIds)
    {
        return $this->afterCreating(function (Ingredient $ingredient) use ($categoryIds) {
            $ingredient->categories()->attach($categoryIds);
        });
    }
}
