<?php

namespace Database\Factories;

use App\Models\ScheduleItem;
use App\Models\Schedule;
use App\Models\SchedulePlace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleItemFactory extends Factory
{
    protected $model = ScheduleItem::class;

    public function definition(): array
    {
        $isRegularDay = $this->faker->boolean(90);
        
        return [
            'id' => $this->faker->uuid(),
            'schedule_id' => Schedule::factory(),
            'schedule_place_id' => SchedulePlace::factory(),
            'day' => $isRegularDay ? $this->faker->numberBetween(0, 6) : null,
            'is_open' => $this->faker->boolean(95),
            'date' => !$isRegularDay ? $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d') : null,
        ];
    }

    /**
     * Indique que c'est un jour régulier (récurrent)
     */
    public function regularDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'day' => $this->faker->numberBetween(0, 6),
            'date' => null,
        ]);
    }

    /**
     * Indique que c'est une date spécifique
     */
    public function specificDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'day' => null,
            'date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
        ]);
    }

    /**
     * Indique que c'est fermé
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open' => false,
        ]);
    }

    /**
     * Indique que c'est ouvert
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open' => true,
        ]);
    }
}
