<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartSnapshot extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'items',
        'original_total',
        'discount_amount',
        'final_total',
        'razorpay_order_id',
        'applied_rules',
    ];

    protected $casts = [
        'items' => 'array',
        'applied_rules' => 'array',
        'original_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
    ];
}
