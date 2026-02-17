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

        // Count distinct users who used this IP
        $count = Order::where('ip_address', $order->ip_address)
            ->distinct('user_id')
            ->count('user_id');

        return $count >= $ruleConfig->threshold_value;
    }

    public function getMessage(): string
    {
        return 'Multiple accounts used from the same IP address.';
    }
}
