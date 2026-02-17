<?php

namespace App\Services\Fraud\Rules;

use App\Models\Order;
use App\Models\FraudRule;

interface RuleInterface
{
    /**
     * Evaluate the rule for a given order.
     *
     * @param Order $order
     * @param FraudRule $ruleConfig
     * @return bool True if rule matches (is suspicious), false otherwise
     */
    public function matches(Order $order, FraudRule $ruleConfig): bool;

    /**
     * Get the message to log when rule matches.
     *
     * @return string
     */
    public function getMessage(): string;
}
