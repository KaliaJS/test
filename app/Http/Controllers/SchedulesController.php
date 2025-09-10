<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Schedule;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::withRelations()->where('is_active', true)->get();

        return ApiResponse::scheduleCollection($schedules);
    }
}
