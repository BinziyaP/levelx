<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'rule_type',
        'threshold_value',
        'time_window_minutes',
        'weight',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold_value' => 'decimal:2',
        'time_window_minutes' => 'integer',
    ];

    public function logs()
    {
        return $this->hasMany(FraudLog::class, 'rule_id');
    }
}
