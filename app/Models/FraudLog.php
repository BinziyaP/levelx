<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'rule_id',
        'score_added',
        'message',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function rule()
    {
        return $this->belongsTo(FraudRule::class, 'rule_id');
    }
}
