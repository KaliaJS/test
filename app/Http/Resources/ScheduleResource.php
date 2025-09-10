<?php

namespace App\Http\Resources;

use App\Http\Resources\ScheduleItemResource;
use App\Http\Resources\TruckResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'truck_id' => $this->truck_id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'truck' => new TruckResource($this->whenLoaded('truck')),
            'schedule_items' => ScheduleItemResource::collection($this->whenLoaded('scheduleItems')),
        ];
    }
}
