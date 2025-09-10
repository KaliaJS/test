<?php

namespace Database\Factories;

use App\Models\Truck;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'truck_id' => Truck::factory(),
            'name' => $this->faker->words(2, true),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
