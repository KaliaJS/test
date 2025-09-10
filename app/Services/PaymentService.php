<?php

namespace App\Services;

use App\Exceptions\TerminalException;
use Stripe\Exception\InvalidRequestException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class PaymentService
{
    public function __construct(
        protected StripeClient $stripe
    ) {}

    public function createForWeb(string $amount): PaymentIntent
    {
        return $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'chf',
            'automatic_payment_methods' => ['enabled' => true],
            'capture_method' => 'automatic'
        ]);
    }

    public function createForTerminal(string $amount): PaymentIntent
    {
        return $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'chf',
            'payment_method_types' => ['card_present'],
            'capture_method' => 'automatic'
        ]);
    }

    public function updateIntent(string $paymentIntentId, string $amount): PaymentIntent
    {
        return $this->stripe->paymentIntents->update($paymentIntentId, ['amount' => $amount]);
    }

    public function retrieveIntent(string $paymentIntentId): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($paymentIntentId);
    }

    public function processForTerminal(string $paymentIntentId, string $readerId, int $tries = 3)
    {
        $attempt = 0;

        do {
            $attempt++;
            try {
                return $this->stripe->terminal->readers->processPaymentIntent($readerId, [
                    'payment_intent' => $paymentIntentId,
                    'process_config' => ['enable_customer_cancellation' => true],
                ]);
            } catch (InvalidRequestException $error) {
                switch ($error->getStripeCode()) {
                    case 'terminal_reader_timeout': if ($attempt >= $tries) throw TerminalException::timeout(); break;
                    case 'terminal_reader_offline': throw TerminalException::offline(); break;
                    case 'terminal_reader_busy': throw TerminalException::busy(); break;
                    case 'intent_invalid_state': throw TerminalException::invalidIntent(); break;
                    case 'resource_missing': throw TerminalException::notFound(); break;
                    default: throw new TerminalException($error->getMessage());
                }
            }
        } while ($attempt < $tries);

        throw new TerminalException('Échec inattendu du paiement.');
    }

    public function refund($paymentIntentId, $amount = null)
    {
        if (empty($paymentIntentId)) {
            throw new \RuntimeException('Le paymentIntentId est obligatoire.');
        }

        $refundData = [
            'payment_intent' => $paymentIntentId,
            ...($amount ? ['amount' => $amount] : []),
        ];

        return $this->stripe->refunds->create($refundData);
    }

    public function getReaders()
    {
        return $this->stripe->terminal->readers->all(['limit' => 20]);
    }

}
