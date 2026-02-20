<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status', // pending, under_review, approved, rejected
        'reason',
        'refund_amount',
        'razorpay_refund_id',
        'seller_id',
        'resolution_type', // full_refund, partial_refund, no_refund
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the buyer (user who placed the order).
     * This is a convenience attribute to avoid deep nesting in views.
     */
    public function getBuyerAttribute()
    {
        return $this->order ? $this->order->user : null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function evidences()
    {
        return $this->hasMany(ReturnEvidence::class, 'order_return_id');
    }

    public function logs()
    {
        return $this->hasMany(ReturnLog::class, 'order_return_id');
    }
}
