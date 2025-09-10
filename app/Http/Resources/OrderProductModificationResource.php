<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductModificationResource extends JsonResource
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
            'id' => $this->id,
            'order_product_id' => $this->order_product_id,
            'ingredient_id' => $this->ingredient_id,
            'ingredient_name' => $this->ingredient_name,
            'action' => $this->action,
            'quantity' => $this->quantity,
            'supplement_price' => $this->supplement_price,
        ];
    }
}
