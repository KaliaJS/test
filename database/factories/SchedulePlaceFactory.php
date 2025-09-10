<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SchedulePlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'coords_latitude' => $this->faker->latitude(),
            'coords_longitude' => $this->faker->longitude(),
        ];
    }
}
