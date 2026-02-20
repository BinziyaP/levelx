<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pick = ['bundle_rules','cart_snapshots','carts','categories','fraud_logs','fraud_rules','notifications'];
foreach ($pick as $name) {
    $cols = DB::select("SHOW COLUMNS FROM `{$name}`");
    echo "T:{$name}\n";
    foreach ($cols as $c) echo "  {$c->Field} {$c->Type} {$c->Key}\n";
    echo "\n";
}
