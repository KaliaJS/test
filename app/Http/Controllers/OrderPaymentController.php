<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    protected $orderService;
    protected $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function refund(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->id);

        $this->paymentService->refund($order->payment_intent_id);
        $this->orderService->refund($order->payment_intent_id, $order->total_amount);

        return ApiResponse::noContent();
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'slug' => 'required|exists:orders,slug',
        ]);
        
        $this->orderService->processPaymentForPreOrder($request->slug);

        return ApiResponse::noContent();
    }

    public function processPaymentFromTerminal(Request $request): ApiResponse
    {
        $request->validate([
            'slug' => 'required|exists:orders,slug',
            'reader_id' => 'required|string'
        ]);

        $this->orderService->processTerminalPayment(
            slug: $request->slug,
            reader_id: $request->reader_id
        );

        return ApiResponse::success('Paiement initié');
    }

    public function addTip(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|exists:orders,payment_intent_id',
            'tip_amount' => 'required|numeric|min:1',
        ]);
        
        $this->orderService->setTip($request->payment_intent_id, $request->tip_amount);
        
        return ApiResponse::noContent();
    }

}
