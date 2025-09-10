<?php

namespace App\Services;

use App\Services\OrderService;
use Stripe\Webhook;

class WebhookService
{
    private const EVENT_HANDLERS = [
        'payment_intent.succeeded' => 'handlePaymentIntentSucceeded',
        'payment_intent.payment_failed' => 'handlePaymentIntentFailed',
        'charge.succeeded' => 'handleChargeSucceeded',
        'charge.refunded' => 'handleChargeRefunded',
        'terminal.reader.action_updated' => 'handleTerminalReaderActionUpdated',
        'terminal.reader.action_failed' => 'handleTerminalReaderActionFailed',
    ];

    public function __construct(
        protected OrderService $orderService
    ) {}

    public function process(string $payload, string $header): void
    {
        $event = Webhook::constructEvent(
            $payload,
            $header,
            config('services.stripe.webhook_secret')
        );

        $handler = self::EVENT_HANDLERS[$event->type] ?? null;

        if ($handler && method_exists($this, $handler)) {
            $this->{$handler}($event->data->object);
        }
    }

    private function handlePaymentIntentSucceeded(object $data): void
    {
        $tipAmount = $data->amount_details->tip->amount ?? null;

        if ($tipAmount && $tipAmount > 0) {
            $this->orderService->setTip($data->id, $tipAmount);
        }

        $this->orderService->markAsPaid($data->id);
    }

    private function handlePaymentIntentFailed(object $paymentIntent): void
    {
        $this->orderService->markAsFailed(
            paymentIntentId: $paymentIntent->id,
            errorCode: $paymentIntent->last_payment_error->code
        );
    }

    private function handleChargeSucceeded(object $charge): void
    {
        $this->orderService->updatePaymentMethod(
            paymentIntentId: $charge->payment_intent,
            paymentMethod: $charge->payment_method_details
        );
    }

    private function handleChargeRefunded(object $charge): void
    {
        $this->orderService->processRefund(
            paymentIntentId: $charge->payment_intent, 
            $charge->amount_refunded
        );
    }

    private function handleTerminalReaderActionupdated(object $data): void
    {
        
    }

    private function handleTerminalReaderActionFailed(object $data): void
    {
        $failureCode = $data->action->failure_code ?? null;
        $paymentIntent = $data->action->process_payment_intent->payment_intent ?? null;

        // if ($failureCode === OrderStatus::CUSTOMER_CANCELED->value && $paymentIntent) {
        //     $this->orderService->markAsCanceledByCustomer($paymentIntent);
        // }
    }

}