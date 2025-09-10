<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleItemResource extends JsonResource
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
            'schedule_id' => $this->schedule_id,
            'schedule_place_id' => $this->schedule_place_id,
            'day' => $this->day,
            'is_open' => (bool) $this->is_open,
            'date' => $this->date,
            'schedule_place' => $this->whenLoaded('schedulePlace', function () {
                return [
                    'id' => $this->schedulePlace->id,
                    'name' => $this->schedulePlace->name,
                    'coords_latitude' => $this->schedulePlace->coords_latitude,
                    'coords_longitude' => $this->schedulePlace->coords_longitude,
                ];
            }),
            'hours' => $this->whenLoaded('hours', function () {
                return $this->hours->map(function ($hour) {
                    return [
                        'id' => $hour->id,
                        'schedule_item_id' => $hour->schedule_item_id,
                        'start_at' => $hour->start_at,
                        'end_at' => $hour->end_at,
                    ];
                });
            }),
        ];
    }
}
