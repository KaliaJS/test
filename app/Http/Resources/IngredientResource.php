<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'organic_type' => $this->organic_type,
            'is_swiss' => $this->is_swiss,
            'supplement_price' => $this->supplement_price,
            'max_supplement' => $this->max_supplement,
            'is_removable' => $this->is_removable,
            'prepared_at' => $this->prepared_at,
            'pivot' => $this->whenPivotLoaded('ingredient_product', fn () => [
                'is_showed' => $this->pivot->is_showed,
                'quantity' => $this->pivot->quantity,
                'quantity_format' => $this->pivot->quantity_format,
            ]),
        ];
    }
}
