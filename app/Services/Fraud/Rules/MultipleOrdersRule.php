<?php

namespace App\Services\Fraud\Rules;

use App\Models\Order;
use App\Models\FraudRule;
use Carbon\Carbon;

class MultipleOrdersRule implements RuleInterface
{
    public function matches(Order $order, FraudRule $ruleConfig): bool
    {
        if (!$order->user_id) {
            return false;
        }

        // 1. Calculate Start Time
        $startTime = $order->created_at->copy()->subMinutes((int) $ruleConfig->time_window_minutes);

        // 2. Count total orders in the window (including current one)
        $count = Order::where('user_id', $order->user_id)
            ->where('created_at', '>=', $startTime)
            ->where('created_at', '<=', $order->created_at)
            ->count();

        // 3. Log for debugging
        \Illuminate\Support\Facades\Log::info("MultipleOrdersRule Check:", [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'time_window_min' => $ruleConfig->time_window_minutes,
            'start_time' => $startTime->toDateTimeString(),
            'order_time' => $order->created_at->toDateTimeString(),
            'other_orders_count' => $count,
            'threshold' => $ruleConfig->threshold_value,
            'match' => $count >= $ruleConfig->threshold_value ? 'YES' : 'NO'
        ]);

        // 4. Compare: If user has X previous orders, and X >= Threshold, then it's suspicious.
        // Example: Threshold 4. User places 5th order. Previous count is 4. 4 >= 4 is TRUE.
        return $count >= $ruleConfig->threshold_value;
    }

    public function getMessage(): string
    {
        return 'Unusual number of orders placed within short time window.';
    }
}
