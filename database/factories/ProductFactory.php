<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 50000),
            'type' => $this->faker->numberBetween(1, 3),
            'is_homemade' => $this->faker->boolean(),
            'image_path' => $this->faker->optional(0.9)->imageUrl(),
            'manufacturing_time' => $this->faker->optional(0.7)->numberBetween(1, 30),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'profit_margin' => $this->faker->numberBetween(30, 50),
            'updated_at' => fn (array $attributes) => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
        ];
    }
}
