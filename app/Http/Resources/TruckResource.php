<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TruckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_ready' => $this->is_ready,
            'coords_latitude' => $this->coords_latitude,
            'coords_longitude' => $this->coords_longitude,
            'updated_at' => $this->updated_at,
        ];
    }
}
