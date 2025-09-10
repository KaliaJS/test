<?php

namespace App\Http\Controllers;

use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    public function __construct(
        protected WebhookService $WebhookService
    ) {}

    public function endpoint(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $header = $request->header('Stripe-Signature');

        try {
            $this->WebhookService->process($payload, $header);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
