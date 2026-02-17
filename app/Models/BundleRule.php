<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleRule extends Model
{
    use HasFactory;

    const TYPE_GLOBAL = 'global';
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';

    protected $fillable = [
        'name',
        'type',
        'target_id',
        'min_items',
        'max_items',
        'discount_percentage',
        'discount_type',
        'discount_value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_percentage' => 'decimal:2',
    ];
}
