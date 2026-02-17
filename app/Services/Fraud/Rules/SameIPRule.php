<?php

namespace App\Services\Fraud\Rules;

use App\Models\Order;
use App\Models\FraudRule;

class SameIPRule implements RuleInterface
{

    public function matches(Order $order, FraudRule $ruleConfig): bool
    {
        if (!$order->ip_address) {
            return false;
        }

        $query = Order::where('ip_address', $order->ip_address);

        // Apply Time Window if configured
        if ($ruleConfig->time_window_minutes > 0) {
            $startTime = $order->created_at->copy()->subMinutes((int) $ruleConfig->time_window_minutes);
            $query->where('created_at', '>=', $startTime);
        }

        // Count total orders from this IP (Velocity Check)
        // Previous logic counted distinct users, which missed single-user spam.
        // New logic counts all orders from the IP.
        $count = $query->count();

        return $count >= $ruleConfig->threshold_value;
    }

    public function getMessage(): string
    {
        return 'Suspicious activity detected from this IP address.';
    }

}
