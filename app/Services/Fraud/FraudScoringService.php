<?php

namespace App\Services\Fraud;

use App\Models\Order;
use App\Models\FraudRule;
use App\Models\FraudLog;
use App\Services\Fraud\Rules\HighCartValueRule;
use App\Services\Fraud\Rules\MultipleOrdersRule;
use App\Services\Fraud\Rules\SameIPRule;
use App\Services\Fraud\Rules\HighDiscountRule;
use Illuminate\Support\Facades\Log;

class FraudScoringService
{
    protected $rulesMapping = [
        'cart_value' => HighCartValueRule::class,
        'multiple_orders' => MultipleOrdersRule::class,
        'same_ip' => SameIPRule::class,
        'high_discount' => HighDiscountRule::class,
    ];

    public function evaluate(Order $order)
    {
        try {
            Log::info("Starting fraud evaluation for Order ID: {$order->id}");

            $activeRules = FraudRule::where('is_active', true)->get();
            $totalScore = 0;
            $logs = [];

            foreach ($activeRules as $ruleConfig) {
                if (!isset($this->rulesMapping[$ruleConfig->rule_type])) {
                    Log::warning("No rule class found for type: {$ruleConfig->rule_type}");
                    continue;
                }

                $ruleClass = $this->rulesMapping[$ruleConfig->rule_type];
                $rule = new $ruleClass();

                if ($rule->matches($order, $ruleConfig)) {
                    $totalScore += $ruleConfig->weight;
                    
                    $logs[] = [
                        'order_id' => $order->id,
                        'rule_id' => $ruleConfig->id,
                        'score_added' => $ruleConfig->weight,
                        'message' => $rule->getMessage(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    Log::info("Fraud rule matched: {$ruleConfig->rule_name} (+{$ruleConfig->weight})");
                }
            }

            // Batch insert logs
            if (!empty($logs)) {
                FraudLog::insert($logs);
            }

            // Update order
            $systemThreshold = config('fraud.system_threshold', 100);
            $isSuspicious = $totalScore >= $systemThreshold;

            $order->update([
                'fraud_score' => $totalScore,
                'is_suspicious' => $isSuspicious
            ]);

            Log::info("Fraud evaluation completed. Score: {$totalScore}. Suspicious: " . ($isSuspicious ? 'Yes' : 'No'));

        } catch (\Exception $e) {
            Log::error('Fraud Evaluation Failed for Order ' . $order->id . ': ' . $e->getMessage());
            // Do not rethrow, ensuring order process continues
        }
    }
}
