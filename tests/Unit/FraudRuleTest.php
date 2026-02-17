<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\FraudRule;
use App\Services\Fraud\Rules\HighDiscountRule;
use App\Services\Fraud\Rules\HighCartValueRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FraudRuleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_high_discount_rule_detects_suspicious_discount()
    {
        $order = new Order();
        $order->original_price = 10000;
        $order->discount_amount = 6000; // 60% discount
        $order->total_price = 4000;

        $rule = new HighDiscountRule();
        $config = new FraudRule(); // Config not used by this rule but required by signature

        $this->assertTrue($rule->matches($order, $config));
    }

    public function test_high_discount_rule_ignores_normal_discount()
    {
        $order = new Order();
        $order->original_price = 10000;
        $order->discount_amount = 2000; // 20% discount
        $order->total_price = 8000;

        $rule = new HighDiscountRule();
        $config = new FraudRule();

        $this->assertFalse($rule->matches($order, $config));
    }

    public function test_high_cart_value_uses_original_price()
    {
        $order = new Order();
        $order->original_price = 50000;
        $order->total_price = 40000; // Discounted below threshold

        $rule = new HighCartValueRule();
        $config = new FraudRule();
        $config->threshold_value = 45000;

        // Should match because original_price (50000) >= threshold (45000)
        $this->assertTrue($rule->matches($order, $config));
    }
    
    public function test_high_cart_value_fallback_to_total_price()
    {
        $order = new Order();
        $order->original_price = null;
        $order->total_price = 50000;

        $rule = new HighCartValueRule();
        $config = new FraudRule();
        $config->threshold_value = 45000;

        $this->assertTrue($rule->matches($order, $config));
    }
}
