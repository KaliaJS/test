<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientsController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::all();

        return ApiResponse::ingredientCollection($ingredients);
    }
}
