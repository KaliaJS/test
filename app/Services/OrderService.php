<?php

namespace App\Services;

use App\Enums\PAYMENT_FAILED;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\OrderProductModification;
use App\Models\Product;
use App\Services\CustomerService;
use App\Services\FoodWordGenerator;
use App\Services\PaymentService;
use App\Services\UserCustomerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const COLLECTION_CODE_LENGTH = 3;
    private const COLLECTION_CODE_MAX = 999;

    public function __construct(
        protected PaymentService $paymentService,
        protected CustomerService $customerService,
        protected Order $order,
        protected FoodWordGenerator $foodwordGenerator,
        protected OrderCalculator $calculator,
        protected OrderProductBuilder $productBuilder,
    ) {}

    public function create(
        array $validatedProducts,
        ?string $userId = null,
        ?string $guestId = null,
        string $for = 'web'
    ): Order {
        return DB::transaction(fn() => $this->processOrder(
            $validatedProducts,
            $userId,
            $guestId,
            $for
        ));
    }

    private function processOrder(
         array $validatedProducts,
         ?string $userId,
         ?string $guestId,
         string $for
     ): Order {
        $products = $this->loadProducts($validatedProducts);
        $orderData = $this->productBuilder->buildOrderData($validatedProducts, $products);
        $paymentIntent = $this->createPaymentIntent($for, $orderData['totalAmount']);

        $order = $this->createOrder($userId, $guestId, $orderData, $paymentIntent->id);
        $this->attachProducts($order, $orderData);

        return $order->loadMissing('products.modifications', 'products.product');
    }

    private function loadProducts(array $items): Collection
    {
        $productIds = collect($items)->pluck('product_id')->unique();

        return Product::with('ingredients')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');
    }

    private function createPaymentIntent(string $for, int $amount): object
    {
        return match($for) {
            'web' => $this->paymentService->createForWeb($amount),
            'terminal' => $this->paymentService->createForTerminal($amount),
            default => throw OrderException::invalidPaymentMethod($for),
        };
    }

    private function createOrder(
        ?string $userId,
        ?string $guestId,
        array $orderData,
        string $paymentIntentId
    ): Order {
        return Order::create([
            'slug' => $this->foodwordGenerator->create(3),
            'user_id' => $userId ?? null,
            'guest_id' => $userId ? null : $guestId,
            'total_amount' => $orderData['totalAmount'],
            'total_manufacturing_time' => $orderData['totalManufacturingTime'],
            'payment_intent_id' => $paymentIntentId,
            'payment_collected_code' => $this->generateCollectionCode(),
        ]);
    }

    private function attachProducts(Order $order, array $orderData): void
    {
        $createdProducts = $order->products()->createMany($orderData['products']);
        
        if (!empty($orderData['modifications'])) {
            $this->attachModifications($createdProducts, $orderData['modifications']);
        }
    }

    private function attachModifications(Collection $orderProducts, array $modificationsMap): void
    {
        $allModifications = collect($orderProducts)
            ->flatMap(fn($product, $index) => 
                collect($modificationsMap[$index] ?? [])
                    ->map(fn($mod) => array_merge($mod, [
                        'order_product_id' => $product->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]))
            )
            ->toArray();

        if (!empty($allModifications)) {
            OrderProductModification::insert($allModifications);
        }
    }

    private function generateCollectionCode(): string
    {
        return str_pad(
            random_int(0, self::COLLECTION_CODE_MAX), 
            self::COLLECTION_CODE_LENGTH, 
            '0', 
            STR_PAD_LEFT
        );
    }

    public function refund(string $paymentIntentId, int $amountRefunded)
    {
        return $this->order->where('payment_intent_id', $paymentIntentId)
            ->update([
                'refunded_amount' => $amountRefunded,
                'refunded_at' => Carbon::now(),
            ]);
    }

    public function setTip(string $paymentIntentId, float $tipAmount)
    {
        $order = $this->order->where('payment_intent_id', $paymentIntentId)->firstOrFail();
        $order->tip_amount = $tipAmount;
        $order->save();
    }

    public function markAsFailed(string $paymentIntentId, $errorCode): void
    {
        $order = $this->order->where('payment_intent_id', $paymentIntentId)->firstOrFail();
        $order->payment_error_code = $errorCode;
        $order->save();
    }

    public function updatePaymentMethod(string $paymentIntentId, string $paymentMethod): void
    {
        $order = $this->order->where('payment_intent_id', $paymentIntentId)->firstOrFail();
        $order->payment_method = $paymentMethod;
        $order->save();
    }

    public function markAsPaid(string $paymentIntentId): void
    {
        $order = $this->order->where('payment_intent_id', $paymentIntentId)->first();

        if (!$order->paid_at) {
            $order->payment_error_code = null;
            $order->paid_at = Carbon::now();
            $order->save();
        }
    }

    public function processTerminalPayment(string $slug, string $reader_id)
    {
        $order = $this->order->where('slug', $slug)->first();

        if (! $order) {
            throw OrderException::notFound();
        }

        $paymentIntent = $order->payment_intent_id
            ? $this->paymentService->retrieveIntent($order->payment_intent_id)
            : $this->paymentService->createForTerminal($order->total_amount);

        if ($paymentIntent->status === 'succeeded') {
            throw OrderException::alreadyPaid();
        }

        if ($paymentIntent->status === 'canceled') {
            $paymentIntent = $this->paymentService->createForTerminal($order->total_amount);
        }

        $order->payment_intent_id = $paymentIntent->id;
        $order->save();

        $this->paymentService->processForTerminal($paymentIntent->id, $reader_id);
    }
}
