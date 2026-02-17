<?php

namespace App\Services\Fraud\Rules;

use App\Models\Order;
use App\Models\FraudRule;

class HighCartValueRule implements RuleInterface
{
    public function matches(Order $order, FraudRule $ruleConfig): bool
    {
        return $order->total_price >= $ruleConfig->threshold_value;
    }

    public function getMessage(): string
    {
        return 'Order value exceeds high value threshold.';
    }
}
