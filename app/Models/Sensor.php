<?php

namespace App\Models;

use App\Enums\SensorType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasFactory;

    protected $casts = [
        'last_temp' => 'float',
        'min_temp_alert' => 'float',
        'max_temp_alert' => 'float',
        'type' => SensorType::class,
    ];

    public function measurements(): HasMany
    {
        return $this->hasMany(SensorMeasurement::class);
    }

    protected function mac(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => strtoupper($value),
            set: fn(?string $value) => strtoupper(trim($value)),
        );
    }

    protected function batteryMv(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value / 1000,
        );
    }

    protected function batteryPercent(): Attribute
    {
        return Attribute::make(
            get: function () {
                $voltage = $this->battery_mv;

                $maxV = 3.0;
                $minV = 2.5;

                $percent = (($voltage - $minV) / ($maxV - $minV)) * 100;

                if ($percent > 100) $percent = 100;
                if ($percent < 0) $percent = 0;

                return round($percent);
            }
        );
    }
}
