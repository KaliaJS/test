<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\CustomerService;
use App\Services\RewardService;

class OrderObserver
{
    public function __construct(
        private RewardService $rewardService,
        private CustomerService $customerService
    ) {}

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($this->justTransitionedTo($order, 'paid_at')) {
            $this->handlePaymentConfirmed($order);
        }
        
        if ($this->justTransitionedTo($order, 'refunded_at')) {
            $this->handleRefund($order);
        }
    }

    private function justTransitionedTo(Order $order, string $field): bool
    {
        return $order->wasChanged($field) 
            && $order->$field !== null 
            && $order->getOriginal($field) === null;
    }

    private function handlePaymentConfirmed(Order $order): void
    {
        $this->rewardService->addPointsForOrder($order);
        $this->customerService->update($order);
    }
    
    private function handleRefund(Order $order): void
    {
        $this->rewardService->removePointsForOrder($order);
        $this->customerService->update($order);
    }
}
