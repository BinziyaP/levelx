<?php

namespace App\Services\Fraud\Rules;

use App\Models\Order;
use App\Models\FraudRule;

class HighCartValueRule implements RuleInterface
{
    public function matches(Order $order, FraudRule $ruleConfig): bool
    {
        $valueToCheck = $order->original_price ?? $order->total_price;
        return $valueToCheck >= $ruleConfig->threshold_value;
    }

    public function getMessage(): string
    {
        return 'Order value exceeds high value threshold.';
    }
}
