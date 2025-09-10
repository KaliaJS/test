<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use App\Http\Resources\RewardPointResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'total_points' => $this->total_points,
            'updated_at' => $this->updated_at,
            'history' => RewardPointResource::collection($this->whenLoaded('rewardPoints')),
        ];
    }
}
