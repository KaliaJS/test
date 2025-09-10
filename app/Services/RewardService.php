<?php

namespace App\Services;

use App\Models\Order;
use App\Models\RewardPoint;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RewardService
{
    const POINTS_PER_CHF = 5;
    
    public function calculatePointsForOrder(Order $order): int
    {
        $amount = $order->total_amount / 100;

        return (int) floor($amount * self::POINTS_PER_CHF);
    }
    
    public function addPointsForOrder(Order $order): void
    {
        if (!$order->user_id) {
            return;
        }

        $order->loadMissing('user');

        if ($order->rewardPoints()->where('points', '>', 0)->exists()) {
            return;
        }
        
        $points = $this->calculatePointsForOrder($order);
        $this->addPoints($order->user, $points, $order);
    }
    
    public function removePointsForOrder(Order $order): void
    {
        if (!$order->user_id) {
            return;
        }

        $order->loadMissing('user');

        if ($order->rewardPoints()->where('points', '<', 0)->exists()) {
            return;
        }
        
        $points = $this->calculatePointsForOrder($order);
        
        try {
            $this->deductPoints($order->user, $points, $order);
        } catch (\Exception $e) {
            $reward = $order->user->reward;
            if ($reward) {
                $reward->update(['total_points' => 0]);
            }
        }
    }
    
    public function addPoints(User $user, int $points, ?Order $order = null): void
    {
        DB::transaction(function () use ($user, $points, $order) {
            $reward = $user->reward()->firstOrCreate(
                [],
                ['total_points' => 0]
            );
            
            $reward->rewardPoints()->create([
                'order_id' => $order?->id,
                'points' => $points,
            ]);
            
            $reward->increment('total_points', $points);
        });
    }

    public function deductPoints(User $user, int $points, ?Order $order = null): void
    {
        DB::transaction(function () use ($user, $points, $order) {
            $reward = $user->reward;
            
            if (!$reward || $reward->total_points < $points) {
                throw new \Exception('Points insuffisants');
            }
            
            $reward->rewardPoints()->create([
                'order_id' => $order?->id,
                'points' => -$points,
            ]);
            
            $newTotal = max(0, $reward->total_points - $points);
            $reward->update(['total_points' => $newTotal]);
        });
    }

    public function expireOldPoints(): int
    {
        $oneYearAgo = Carbon::now()->subYear();
        
        return DB::transaction(function () use ($oneYearAgo) {
            DB::statement("
                UPDATE rewards r
                INNER JOIN (
                    SELECT reward_id, SUM(points) as total_expired
                    FROM reward_points
                    WHERE created_at <= ? AND points > 0
                    GROUP BY reward_id
                ) expired ON r.id = expired.reward_id
                SET r.total_points = GREATEST(0, r.total_points - expired.total_expired)
            ", [$oneYearAgo]);
            
            return RewardPoint::where('created_at', '<=', $oneYearAgo)
                ->where('points', '>', 0)
                ->delete();
        });
    }
}
