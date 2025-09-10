<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\ScheduleItem;
use App\Models\ScheduleItemHour;
use App\Models\SchedulePlace;
use Illuminate\Database\Seeder;

class ScheduleItemSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = Schedule::all();
        $places = SchedulePlace::all();

        if ($places->isEmpty()) {
            $this->command->warn('Aucune place trouvée. Exécutez SchedulePlaceSeeder d\'abord.');
            return;
        }

        foreach ($schedules as $schedule) {
            // Stratégie 1: Créer un planning hebdomadaire régulier
            if (rand(0, 1)) {
                $this->createWeeklySchedule($schedule, $places);
            } 
            // Stratégie 2: Créer des dates spécifiques
            else {
                $this->createSpecificDatesSchedule($schedule, $places);
            }
        }
    }

    /**
     * Créer un planning hebdomadaire régulier
     */
    private function createWeeklySchedule($schedule, $places): void
    {
        // Jours de la semaine où le truck est actif (ex: du mardi au samedi)
        $activeDays = collect([2, 3, 4, 5, 6]); // Mardi à Samedi
        
        foreach ($activeDays as $day) {
            // Choisir une place aléatoire pour chaque jour
            $place = $places->random();
            
            // Décider si ouvert ce jour-là (90% de chance)
            $isOpen = rand(1, 10) <= 9;
            
            $scheduleItem = ScheduleItem::create([
                'id' => fake()->uuid(),
                'schedule_id' => $schedule->id,
                'schedule_place_id' => $place->id,
                'day' => $day,
                'is_open' => $isOpen,
                'date' => null,
            ]);

            if ($isOpen) {
                $this->createHoursForItem($scheduleItem);
            }
        }
    }

    /**
     * Créer un planning avec des dates spécifiques
     */
    private function createSpecificDatesSchedule($schedule, $places): void
    {
        // Créer entre 5 et 15 dates spécifiques
        $numberOfDates = rand(5, 15);
        
        for ($i = 0; $i < $numberOfDates; $i++) {
            $place = $places->random();
            $date = fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d');
            $isOpen = rand(1, 10) <= 9; // 90% de chance d'être ouvert
            
            $scheduleItem = ScheduleItem::create([
                'id' => fake()->uuid(),
                'schedule_id' => $schedule->id,
                'schedule_place_id' => $place->id,
                'day' => null,
                'is_open' => $isOpen,
                'date' => $date,
            ]);

            if ($isOpen) {
                $this->createHoursForItem($scheduleItem);
            }
        }
    }

    /**
     * Créer les horaires pour un ScheduleItem
     */
    private function createHoursForItem($scheduleItem): void
    {
        // Décider du nombre de plages horaires (1 ou 2 généralement)
        $numberOfSlots = rand(1, 100) <= 70 ? 1 : 2; // 70% chance d'avoir une seule plage
        
        if ($numberOfSlots === 1) {
            // Une seule plage horaire (journée continue)
            ScheduleItemHour::create([
                'id' => fake()->uuid(),
                'schedule_item_id' => $scheduleItem->id,
                'start_at' => $this->randomTime('morning'),
                'end_at' => $this->randomTime('evening'),
            ]);
        } else {
            // Deux plages horaires (matin et après-midi avec pause déjeuner)
            // Matin
            ScheduleItemHour::create([
                'id' => fake()->uuid(),
                'schedule_item_id' => $scheduleItem->id,
                'start_at' => $this->randomTime('morning'),
                'end_at' => $this->randomTime('lunch'),
            ]);
            
            // Après-midi/Soir
            ScheduleItemHour::create([
                'id' => fake()->uuid(),
                'schedule_item_id' => $scheduleItem->id,
                'start_at' => $this->randomTime('afternoon'),
                'end_at' => $this->randomTime('evening'),
            ]);
        }
    }

    /**
     * Générer une heure aléatoire selon le moment de la journée
     */
    private function randomTime($period): string
    {
        $times = [
            'morning' => ['08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00'],
            'lunch' => ['12:00:00', '12:30:00', '13:00:00', '13:30:00'],
            'afternoon' => ['14:00:00', '14:30:00', '15:00:00', '15:30:00'],
            'evening' => ['18:00:00', '19:00:00', '20:00:00', '21:00:00', '22:00:00'],
        ];
        
        return $times[$period][array_rand($times[$period])];
    }
}
