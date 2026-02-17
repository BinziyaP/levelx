<?php

return [
    'system_threshold' => 100, // Score above which an order is marked suspicious
    
    // Default values for new rules if needed
    'defaults' => [
        'high_cart_value' => 10000,
        'multiple_orders_count' => 3,
        'multiple_orders_time' => 10, // minutes
        'same_ip_users_count' => 3,
    ],
];
