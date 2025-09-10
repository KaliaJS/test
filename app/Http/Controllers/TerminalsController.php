<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderException;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TerminalResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TerminalsController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected OrderService $orderService
    ) {}

    public function fetchs(Request $request)
    {
        ['data' => $terminals] = $this->paymentService->getReaders();

        return ApiResponse::success(
            message: 'Terminals récupérées avec succès',
            data: $terminals
        );
    }

    public function fetchOrders(Request $request)
    {
        $threeDaysAgo = Carbon::now()->subDays(3);

        $orders = Order::withRelations()
            ->where('created_at', '>=', $threeDaysAgo)
            ->get();

        return ApiResponse::orderCollection($orders);
    }

    public function retrieveOrderBySlug(Request $request)
    {
        $order = Order::withRelations()
            ->whereSlug($request->slug)
            ->whereNull('paid_at')
            ->first();

        if (!$order) {
            throw OrderException::notFound();
        }

        $total = 0;

        $grouped = $order->products->groupBy('product_id');

        foreach ($grouped as $productId => $orderProductGroup) {
            $product = $orderProductGroup->first()->product;

            $stock = $product?->stock ?? 0;

            $totalRequested = $orderProductGroup->sum('quantity');

            $available = is_null($product) || is_null($product->stock) ? $totalRequested : min($stock, $totalRequested);

            foreach ($orderProductGroup as $orderProduct) {
                if ($available >= $orderProduct->quantity) {
                    $orderProduct->available_quantity = $orderProduct->quantity;
                    $available -= $orderProduct->quantity;
                } else {
                    $orderProduct->available_quantity = $available;
                    $available = 0;
                }

                $total += $orderProduct->available_quantity * $orderProduct->price;
            }
        }

        $order->total_amount = $total;
        $order->save();

        return ApiResponse::success(
            message: 'Order par slug récupérées avec succès',
            data: new OrderResource($order)
        );
    }

}
