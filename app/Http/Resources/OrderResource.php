<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'slug' => $this->slug,
            'user_id' => $this->user_id,
            'guest_id' => $this->guest_id,
            'total_amount' => $this->total_amount,
            'tip_amount' => $this->tip_amount,
            'refunded_amount' => $this->refunded_amount,
            'payment_intent_id' => $this->payment_intent_id,
            'total_manufacturing_time' => $this->total_manufacturing_time,
            'payment_error_code' => $this->payment_error_code,
            'payment_collected_code' => $this->payment_collected_code,
            'paid_at' => $this->paid_at,
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'collected_at' => $this->collected_at,
            'refunded_at' => $this->refunded_at,
            'created_at' => $this->created_at,
            'products' => OrderProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
