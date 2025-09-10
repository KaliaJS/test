<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Rules\ExistsInArray;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['ingredients' => function ($query) {
            $query->wherePivot('is_showed', true);
        }])->get();

        return ApiResponse::productCollection($products);
    }

    public function addIngredients(Request $request) {
        $inputs = (object) $request->validate([
            'productId' => 'required|uuid|exists:products,id',
            'ingredientIds' => ['required', 'array', 'min:1', new ExistsInArray('ingredients')],
            'ingredientIds.*' => 'required|uuid',
        ]);
        
        $product = Product::whereId($inputs->productId)->firstOrFail();
        $product->ingredients()->syncWithoutDetaching($inputs->ingredientIds);
        $product->broadcastUpdated();
        
        return ApiResponse::noContent();
    }

    public function removeIngredient(Request $request) {
        $inputs = (object) $request->validate([
            'productId' => 'required|uuid|exists:products,id',
            'ingredientId' => 'required|uuid|exists:ingredients,id',
        ]);
        
        $product = Product::whereId($inputs->productId)->firstOrFail();
        $product->ingredients()->detach($inputs->ingredientId);
        $product->broadcastUpdated();
        
        return ApiResponse::noContent();
    }
}
