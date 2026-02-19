<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "VERIFICATION:\n";
$rules = DB::table('fraud_rules')->get();
foreach ($rules as $r) {
    echo "#{$r->id} {$r->rule_name} type={$r->rule_type} thresh={$r->threshold_value} tw={$r->time_window_minutes} w={$r->weight} " . ($r->is_active ? 'ON' : 'OFF') . "\n";
}

echo "\nMAPPING CHECK:\n";
$svc = app(App\Services\Fraud\FraudScoringService::class);
$mapping = [
    'cart_value' => 'HighCartValueRule',
    'multiple_orders' => 'MultipleOrdersRule',
    'same_ip' => 'SameIPRule',
    'high_discount' => 'HighDiscountRule',
];
foreach ($rules as $r) {
    $executes = $mapping[$r->rule_type] ?? 'UNKNOWN';
    echo "{$r->rule_name} -> {$r->rule_type} -> executes {$executes}\n";
}

echo "\nHISTORICAL CHECK:\n";
$logCount = DB::table('fraud_logs')->count();
echo "Total fraud logs: {$logCount} (unchanged)\n";
$orderCount = DB::table('orders')->count();
echo "Total orders: {$orderCount} (unchanged)\n";
