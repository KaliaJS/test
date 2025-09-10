<?php

namespace App\Http\Resources;

use App\Http\Resources\OrderProductModificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'available_quantity' => $this->available_quantity ?? $this->quantity,
            'price' => $this->price,
            'is_done' => $this->is_done,
            'modifications' => OrderProductModificationResource::collection($this->whenLoaded('modifications')),
            'product' => new ProductResource($this->whenLoaded('product'))
        ];
    }
}
