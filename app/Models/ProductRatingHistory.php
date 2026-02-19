<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRatingHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'product_rating_history';

    protected $fillable = [
        'product_id',
        'average_rating',
        'total_reviews',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
