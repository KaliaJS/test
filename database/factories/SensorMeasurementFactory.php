<?php

namespace Database\Factories;

use App\Models\Sensor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorMeasurementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sensor_id' => Sensor::factory(),
            'sequence' => $this->faker->optional()->numberBetween(2800, 3300),
            'temp' => $this->faker->randomFloat(2, -20, 20),
            'measured_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
