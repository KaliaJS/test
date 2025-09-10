<?php
namespace Database\Seeders;

use App\Models\SchedulePlace;
use Illuminate\Database\Seeder;

class SchedulePlaceSeeder extends Seeder
{
    public function run()
    {
        SchedulePlace::factory(12)->create();
    }
}