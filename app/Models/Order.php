<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'items',
        'total_price',
        'status',
        'payment_id',
        'shipping_status', 'tracking_number', 'courier_code', 'tracking_response',
        'packed_at', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'items' => 'array',
        'tracking_response' => 'array',
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
