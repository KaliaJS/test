<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TerminalResource extends JsonResource
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
            'object' => $this->object,
            'label' => $this->label,
            'status' => $this->status,
            'livemode' => $this->livemode,
            'location' => $this->location,
            'serial_number' => $this->serial_number,
            'device_type' => $this->device_type,
            'device_sw_version' => $this->device_sw_version,
            'ip_address' => $this->ip_address,
            'last_seen_at' => $this->last_seen_at,
            'action' => $this->action,
            'metadata' => $this->metadata,
        ];
    }
}
