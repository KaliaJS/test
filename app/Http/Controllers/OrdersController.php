<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductModification;
use App\Models\Product;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Order::query()->withRelations();
        $user = $request->user();

        $user?->id
            ? $query->whereUserId($user?->id)
            : $query->whereGuestId($user?->guest_id);

        return ApiResponse::orderCollection($query->get());
    }

    public function show(Request $request)
    {
        $query = Order::query()->withRelations()->whereId($request->id);
        $user = $request->user();

        $user?->id
            ? $query->whereUserId($user?->id)
            : $query->whereGuestId($user?->guest_id);

        return ApiResponse::order($query->get());
    }

    public function create(StoreOrderRequest $request)
    {
        $order = $this->orderService->create(
            validatedProducts: $request->safe()->products,
            userId: $request->user()?->id,
            guestId: $request->user()?->guest_id
        );

        return ApiResponse::order($order);
    }

    public function totalManufacturingTime(): ApiResponse
    {
        $totalTime = (float) Order::whereNotNull('paid_at')
                         ->whereNull('finished_at')
                         ->sum('total_manufacturing_time');

        return ApiResponse::success(
            message: 'Temps de production récupéré avec succès',
            data: $totalTime
        );
    }

}
