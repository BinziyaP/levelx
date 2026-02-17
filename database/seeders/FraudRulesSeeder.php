<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FraudRule;

class FraudRulesSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            [
                'rule_name' => 'High Cart Value - Critical',
                'rule_type' => 'cart_value',
                'threshold_value' => 50000,
                'weight' => 50,
                'is_active' => true,
            ],
            [
                'rule_name' => 'High Cart Value - Warning',
                'rule_type' => 'cart_value',
                'threshold_value' => 20000,
                'weight' => 20,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Multiple Orders (Rapid)',
                'rule_type' => 'multiple_orders',
                'threshold_value' => 3,
                'time_window_minutes' => 10,
                'weight' => 40,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Same IP Usage',
                'rule_type' => 'same_ip',
                'threshold_value' => 3,
                'weight' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            FraudRule::create($rule);
        }
    }
}
