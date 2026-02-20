<?php
use App\Models\OrderReturn;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$return = OrderReturn::find(4);
if ($return) {
    echo "Status: {$return->status}\n";
    foreach ($return->logs as $log) {
        echo "Log ID: {$log->id}, Old: {$log->old_status}, New: {$log->new_status}, Note: {$log->note}\n";
    }
} else {
    echo "Return #4 not found.\n";
}
