<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\SchedulePlace;
use Illuminate\Http\Request;

class SchedulePlacesController extends Controller
{
    public function index(Request $request)
    {
        $schedulePlaces = SchedulePlace::all();

        return ApiResponse::schedulePlaceCollection($schedulePlaces);
    }
}
