<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\SchedulePlace;
use App\Models\Truck;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        Truck::all()->each(function ($truck) {
            // Créer 1 à 3 schedules par truck
            $numberOfSchedules = rand(1, 3);
            
            for ($i = 0; $i < $numberOfSchedules; $i++) {
                Schedule::create([
                    'id' => fake()->uuid(),
                    'truck_id' => $truck->id,
                    'name' => $this->generateScheduleName($i),
                    'is_active' => $i === 0, // Seul le premier est actif
                ]);
            }
        });
    }
    
    private function generateScheduleName($index): string
    {
        $names = [
            'Planning Principal',
            'Planning Été',
            'Planning Hiver',
            'Planning Événements',
            'Planning Festivals',
        ];
        
        return $names[$index] ?? 'Planning ' . ($index + 1);
    }
}
