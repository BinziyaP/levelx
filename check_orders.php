<?php
$orders = \App\Models\Order::latest()->take(5)->get();
foreach($orders as $o) {
    echo "Order #{$o->id} | status={$o->status} | shipping={$o->shipping_status} | suspicious={$o->is_suspicious}\n";
}
