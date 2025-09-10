<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getUsers(Request $request)
    {
        $users = User::all();

        return ApiResponse::userCollection($users);
    }

    public function getOrders(Request $request)
    {
        $orders = Order::withRelations()
           ->whereNotNull('paid_at')
           ->get();

        return ApiResponse::orderCollection($orders);
    }

    public function getUserOrders(Request $request)
    {
        $userOrders = Order::whereId($request->id)->get();

        return ApiResponse::orderCollection($userOrders);
    }

    public function getSchedules(Request $request)
    {
        $schedules = Schedule::withRelations()->get();

        return ApiResponse::scheduleCollection($schedules);
    }

    public function getTrucks(Request $request)
    {
        $trucks = Truck::all();

        return ApiResponse::truckCollection($trucks);
    }

    public function addTruck(Request $request)
    {
        $datas = $request->validate([
            'name' => 'required|string|unique:trucks,name',
        ]);

        $truck = Truck::create($datas);

        return ApiResponse::truck($truck);
    }

    public function deleteTruck(Request $request)
    {
        $request->validate([
            'id' => 'required|uuid',
        ]);

        $truck = Truck::findOrFail($request->id);
        $truck->delete();

        return ApiResponse::success();
    }
}
