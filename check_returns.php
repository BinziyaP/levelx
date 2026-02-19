<?php
$returns = \App\Models\OrderReturn::all();
echo "Found " . $returns->count() . " return requests:\n";
foreach($returns as $r) {
    echo "  Return #{$r->id} | Order #{$r->order_id} | status={$r->status} | reason={$r->reason}\n";
}
