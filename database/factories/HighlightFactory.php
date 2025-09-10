<?php

namespace Database\Factories;

use App\Models\Highlight;
use Illuminate\Database\Eloquent\Factories\Factory;

class HighlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'image_path' => 'burger.jpg',
        ];
    }
}
