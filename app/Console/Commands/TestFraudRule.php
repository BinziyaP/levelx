<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use App\Models\FraudRule;
use App\Services\Fraud\Rules\MultipleOrdersRule;
use Carbon\Carbon;

class TestFraudRule extends Command
{
    protected $signature = 'test:fraud-rule';
    protected $description = 'Test Multiple Orders Fraud Rule';

    public function handle()
    {
        $this->info("Starting Fraud Rule Test...");

        // 1. Setup User and Rule
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
            $this->info("Created test user: " . $user->id);
        } else {
            $this->info("Using existing user: " . $user->id);
        }

        // Ensure Rule Exists
        $rule = FraudRule::firstOrCreate(
            ['rule_type' => 'multiple_orders'],
            [
                'rule_name' => 'High Order Velocity',
                'threshold_value' => 4,
                'time_window_minutes' => 1,
                'weight' => 50,
                'is_active' => true
            ]
        );
        
        // Update to ensure test parameters
        $rule->update([
            'threshold_value' => 4, 
            'time_window_minutes' => 1,
            'is_active' => true
        ]);

        $this->info("Rule configured: Threshold {$rule->threshold_value}, Window {$rule->time_window_minutes} min");

        // 2. Clean up previous valid orders for this test to avoid noise (Optional, ideally use DB transaction)
        // For safety, let's just create orders with a specific timestamp that we know fits.

        // 3. Create 4 "Past" Orders in the last 30 seconds
        $now = Carbon::now();
        $ordersToCreate = 4;
        
        $this->info("Creating $ordersToCreate past orders...");
        for ($i = 0; $i < $ordersToCreate; $i++) {
             Order::create([
                'user_id' => $user->id,
                'items' => json_encode([]),
                'total_price' => 100,
                'status' => 'paid',
                'payment_id' => 'test_pay_' . $i,
                'ip_address' => '127.0.0.1',
                // Explicitly set created_at to be within window but in the past
                'created_at' => $now->copy()->subSeconds(30 - $i), 
                'updated_at' => $now->copy()->subSeconds(30 - $i),
            ]);
        }

        // 4. Create Current Order (The 5th one)
        $currentOrder = Order::create([
            'user_id' => $user->id,
            'items' => json_encode([]),
            'total_price' => 100,
            'status' => 'paid',
            'payment_id' => 'test_pay_current',
            'ip_address' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->info("Created Current Order ID: " . $currentOrder->id);

        // 5. Run the Rule
        $ruleLogic = new MultipleOrdersRule();
        $isSuspicious = $ruleLogic->matches($currentOrder, $rule);

        // 6. Output Result
        if ($isSuspicious) {
            $this->info("✅ SUCCESS: Rule triggered correctly (Suspicious).");
        } else {
            $this->error("❌ FAILURE: Rule did NOT trigger (Safe).");
        }

        // Cleanup
        $this->info("Cleaning up test orders...");
        Order::where('payment_id', 'like', 'test_pay_%')->delete();
    }
}
