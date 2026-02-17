<?php

namespace App\Services\Fraud\Rules;

use App\Models\Order;
use App\Models\FraudRule;

class HighDiscountRule implements RuleInterface
{
    public function matches(Order $order, FraudRule $ruleConfig): bool
    {
        if ($order->original_price > 0 && $order->discount_amount > 0) {
            $discountPercentage = ($order->discount_amount / $order->original_price) * 100;
            return $discountPercentage > 50;
        }
        return false;
    }

    public function getMessage(): string
    {
        return 'Order has an unusually high discount (> 50%).';
    }
}
