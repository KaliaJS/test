<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected OrderService $orderService
    ) {}

    public function create(CreateOrderRequest $request)
    {
        $this->orderService->create($request->validated()['products']);

        return ApiResponse::success('Order created with success');
    }

    public function process(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        $response = $this->paymentService->process($request->id);

        return ApiResponse::success(
            message: 'Payment processed with success',
            data: $response
        );
    }

    public function retrieveSecret(Request $request)
    {
        $request->validate(['paymentIntentId' => 'required|string']);

        $intent = $this->paymentService->retrieveIntent($request->paymentIntentId);

        return ApiResponse::success(
            message: 'SecretId retrieved with success',
            data: $intent->client_secret
        );
    }

    public function update(Request $request)
    {
        $request->validate(['paymentIntentId' => 'required|string']);

        // TO DO
        // Retrieve le amount en se basant sur l'order, mettre dans le controlleur ordercontroller dans le update et pas ici

        $intent = $this->paymentService->updateIntent($request->paymentIntentId);

        return response()->json([
            'client_secret' => $intent->client_secret,
            'amount' => $intent->amount,
        ]);
    }

}
