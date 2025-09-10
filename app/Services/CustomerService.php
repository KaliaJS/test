<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;

class CustomerService
{
    public function update(Order $order): void
    {
        $column = $order->user_id ? 'user_id' : 'guest_id';
        $value = $order->user_id ?? $order->guest_id;
        
        $stats = Order::where($column, $value)
            ->whereNotNull('paid_at')
            ->selectRaw('
                COUNT(CASE WHEN refunded_at IS NULL THEN 1 END) as total_orders,
                SUM(CASE WHEN refunded_at IS NULL THEN total_amount ELSE 0 END) as total_spent,
                COUNT(CASE WHEN refunded_at IS NOT NULL THEN 1 END) as total_refunds,
                SUM(CASE WHEN refunded_at IS NULL AND created_at >= ? THEN 1 ELSE 0 END) as monthly_orders_count,
                MAX(CASE WHEN refunded_at IS NULL THEN created_at END) as last_order_at
            ', [Carbon::now()->subDays(30)])
            ->first();

        if (!$stats->total_orders) {
            return;
        }

        Customer::updateOrCreate(
            [$column => $value],
            [
                'total_orders' => $stats->total_orders ?? 0,
                'total_spent' => $stats->total_spent ?? 0,
                'total_refunds' => $stats->total_refunds ?? 0,
                'monthly_orders_count' => $stats->monthly_orders_count ?? 0,
                'last_order_at' => $stats->last_order_at,
            ]
        );
    }
}
