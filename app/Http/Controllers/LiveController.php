<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiveController extends Controller
{

    public function startPreparingOrder(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->id);
        if (!$order->started_at) {
            $order->started_at = Carbon::now();
            $order->save();
        }

        return ApiResponse::noContent();
    }

    public function startPreparingAllOrders(Request $request)
    {
        $request->validate([
           'ids' => 'required|array',
           'ids.*' => 'uuid',
        ]);

        $orders = Order::whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            if (!$order->started_at) {
                $order->started_at = Carbon::now();
                $order->save();
            }
        }

        return ApiResponse::noContent();
    }

    public function finishPreparingOrder(Request $request)
    {
        $request->validate(['id' => 'required|exists:orders,id']);

        $order = Order::findOrFail($request->id);
        if (!$order->finished_at) {
            $order->finished_at = Carbon::now();
            $order->save();
        }

        return ApiResponse::noContent();
    }

    public function setProductOfOrderDone(Request $request)
    {
        $product = OrderProduct::whereId($request->orderProductId)
            ->whereHas('order', fn($query) => $query->whereNotNull('started_at'))
            ->firstOrFail();

        $product->is_done = $request->value;
        $product->save();
        $product->order->touch();

        return ApiResponse::success($product->is_done);
    }

}
