<?php

namespace App\Http\Resources;

use App\Http\Resources\IngredientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'price_points' => $this->price_points,
            'type' => $this->type,
            'is_homemade' => $this->is_homemade,
            'organic_type' => $this->organic_type,
            'image_path' => $this->image_path ? Storage::url($this->image_path) : null,
            'manufacturing_time' => $this->manufacturing_time,
            'container_quantity' => $this->container_quantity,
            'container_quantity_format' => $this->container_quantity_format,
            'highlight_quantity' => $this->whenPivotLoaded('highlight_product', fn () => $this->pivot->quantity),
            'profit_margin' => $this->when(Gate::allows('admin'), $this->profit_margin),
            'stock' => $this->stock,
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
        ];
    }
}
