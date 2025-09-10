<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function fetch(Request $request): ApiResponse
    {
        $user = $request->user();

        if (!$user->last_activity_at?->isToday()) {
            $user->forceFill([
                'last_activity_at' => Carbon::today()
            ])->save();
        }

        return ApiResponse::user($user);
    }
}
