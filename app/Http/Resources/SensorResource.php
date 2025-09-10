<?php

namespace App\Http\Resources;

use App\Http\Resources\SensorMeasurementResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mac' => $this->mac,
            'name' => $this->name,
            'battery_mv' => $this->battery_mv,
            'battery_percent' => $this->battery_percent,
            'last_temp' => $this->last_temp,
            'min_temp_alert' => $this->min_temp_alert,
            'max_temp_alert' => $this->max_temp_alert,
            'type' => $this->type,
            'updated_at' => $this->updated_at,
            'measurements' => SensorMeasurementResource::collection($this->whenLoaded('measurements')),
        ];
    }
}
