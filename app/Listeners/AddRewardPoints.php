<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Services\RewardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddRewardPoints implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private RewardService $rewardService
    ) {}

    public function handle(OrderPaid $event): void
    {
        $this->rewardService->addPoints(order: $event->order);
    }
}
