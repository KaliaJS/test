<?php

namespace Database\Seeders;

use App\Models\Sensor;
use App\Models\SensorMeasurement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SensorMeasurementSeeder extends Seeder
{
    public function run()
    {
        Sensor::all()->each(function ($sensor) {
            $startDate = Carbon::now()->subDays(30);

            $measurements = SensorMeasurement::factory()
                ->count(50)
                ->for($sensor)
                ->make();

            $measurements->each(function ($measurement, $index) use ($startDate) {
                $measurement->measured_at = $startDate->copy()->addDays($index);
                $measurement->sequence = $index + 1;
                $measurement->save();
            });
        });
    }
}
