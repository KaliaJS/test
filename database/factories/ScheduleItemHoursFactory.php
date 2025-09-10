<?php

namespace Database\Factories;

use App\Models\ScheduleItemHours;
use App\Models\ScheduleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleItemHoursFactory extends Factory
{

    public function definition(): array
    {
        // Générer des horaires cohérents
        $startHour = $this->faker->numberBetween(6, 20); // Début entre 6h et 20h
        $startMinute = $this->faker->randomElement([0, 15, 30, 45]);
        
        // Durée entre 2 et 8 heures
        $duration = $this->faker->numberBetween(2, 8);
        $endHour = min($startHour + $duration, 23); // Ne pas dépasser 23h
        $endMinute = $this->faker->randomElement([0, 15, 30, 45]);
        
        return [
            'id' => $this->faker->uuid(),
            'schedule_item_id' => ScheduleItem::factory(),
            'start_at' => sprintf('%02d:%02d:00', $startHour, $startMinute),
            'end_at' => sprintf('%02d:%02d:00', $endHour, $endMinute),
        ];
    }

    /**
     * Horaires du matin
     */
    public function morning(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => $this->faker->randomElement(['06:00:00', '07:00:00', '08:00:00']),
            'end_at' => $this->faker->randomElement(['12:00:00', '13:00:00', '14:00:00']),
        ]);
    }

    /**
     * Horaires de l'après-midi/soir
     */
    public function afternoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => $this->faker->randomElement(['14:00:00', '15:00:00', '16:00:00']),
            'end_at' => $this->faker->randomElement(['20:00:00', '21:00:00', '22:00:00']),
        ]);
    }

    /**
     * Horaires de la journée complète
     */
    public function fullDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => $this->faker->randomElement(['08:00:00', '09:00:00']),
            'end_at' => $this->faker->randomElement(['20:00:00', '21:00:00', '22:00:00']),
        ]);
    }
}
