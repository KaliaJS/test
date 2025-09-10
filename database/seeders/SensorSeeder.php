<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    public function run()
    {
        Sensor::factory()->create([
            'mac' => 'F2:75:E4:59:29:D5',
            'name' => 'Frigo',
            'type' => 1,
            'battery_mv' => 2990,
            'last_temp' => 3.00,
            'min_temp_alert' => 1.00,
            'max_temp_alert' => 4.00,
        ]);

        Sensor::factory()->create([
            'mac' => 'E1:4D:B7:2A:67:85',
            'name' => 'Congélateur',
            'type' => 1,
            'battery_mv' => 3000,
            'last_temp' => -18.00,
            'min_temp_alert' => -20.00,
            'max_temp_alert' => -17.00,
        ]);

        Sensor::factory()->create([
            'mac' => 'X0:X0:X0:X0:X0:X1',
            'name' => 'Camion Intérieur',
            'type' => 2,
            'battery_mv' => 2830,
            'last_temp' => 24.00,
            'min_temp_alert' => 2.00,
            'max_temp_alert' => 30.00,
        ]);

        Sensor::factory()->create([
            'mac' => 'X0:X0:X0:X0:X0:X2',
            'name' => 'Camion Extérieur',
            'type' => 2,
            'battery_mv' => 2430,
            'last_temp' => 21.00,
            'min_temp_alert' => null,
            'max_temp_alert' => null,
        ]);
    }
}
