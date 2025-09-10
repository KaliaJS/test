<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\IngredientSeeder::class,
            \Database\Seeders\ProductSeeder::class,
            \Database\Seeders\HighlightSeeder::class,
            \Database\Seeders\TruckSeeder::class,
            \Database\Seeders\SchedulePlaceSeeder::class,
            \Database\Seeders\SchedulePlaceSeeder::class,
            \Database\Seeders\ScheduleSeeder::class,
            \Database\Seeders\ScheduleItemSeeder::class,
            \Database\Seeders\CategorySeeder::class,
            \Database\Seeders\SensorSeeder::class,
            \Database\Seeders\SensorMeasurementSeeder::class,
        ]);
    }
}
