<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'items',
        'total_price',
        'status',
        'tracking_number',
        'courier_code',
        'tracking_response',
        'shipping_status',
        'courier_name',
        'shipped_at',
        'delivered_at',
        // Fraud Detection
        'fraud_score',
        'is_suspicious',
        'ip_address',
        'original_price',
        'discount_amount',
    ];

    protected $casts = [
        'items' => 'array',
        'tracking_response' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_suspicious' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function returnRequest()
    {
        return $this->hasOne(OrderReturn::class);
    }

    public function fraudLogs()
    {
        return $this->hasMany(FraudLog::class);
    }


}
