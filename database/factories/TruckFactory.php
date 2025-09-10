<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TruckFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'is_ready' => $this->faker->boolean(50),
            'coords_latitude' => $this->faker->latitude(),
            'coords_longitude' => $this->faker->longitude(),
        ];
    }
}
