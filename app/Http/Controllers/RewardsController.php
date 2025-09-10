<?php

namespace App\Http\Controllers;

use App\Http\Resources\RewardResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    /**
     * Affiche le total des points de récompense de l'utilisateur actuel.
     *
     * @return ApiResponse
     */
    public function fetchs(Request $request)
    {
        $reward = $request->user()->reward;

        return ApiResponse::reward($reward);
    }
}
