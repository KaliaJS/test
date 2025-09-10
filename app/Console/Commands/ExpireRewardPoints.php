<?php

namespace App\Console\Commands;

use App\Services\RewardService;
use Illuminate\Console\Command;

class ExpireRewardPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rewards:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire reward points older than 12 months';

    /**
     * Execute the console command.
     */
    public function handle(RewardService $rewardService)
    {
        $expired = $rewardService->expireOldPoints();
        
        $this->info("Expired {$expired} reward points.");
        
        return 0;
    }
}
