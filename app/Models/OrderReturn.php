<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status', // pending, approved, rejected, refunded
        'reason',
        'refund_amount',
        'razorpay_refund_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
