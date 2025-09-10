<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardPointResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'points' => $this->points,
            'created_at' => $this->created_at,
        ];
    }
}
