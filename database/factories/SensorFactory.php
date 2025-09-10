<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mac' => $this->faker->unique()->macAddress(),
            'name' => $this->faker->unique()->bothify('Sensor-##??'),
            'type' => $this->faker->numberBetween(1, 2),
            'battery_mv' => $this->faker->optional()->numberBetween(2800, 3300),
            'last_temp' => $this->faker->randomFloat(2, -20, 50),
            'min_temp_alert' => $this->faker->randomFloat(2, -22, 50),
            'max_temp_alert' => $this->faker->randomFloat(2, -22, 50),
        ];
    }
}
