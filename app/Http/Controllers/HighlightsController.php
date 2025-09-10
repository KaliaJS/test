<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Highlight;
use Illuminate\Http\Request;

class HighlightsController extends Controller
{
    public function index(Request $request)
    {
        $highlights = Highlight::with('products', 'products.ingredients')->get();

        return ApiResponse::highlightCollection($highlights);
    }
}
