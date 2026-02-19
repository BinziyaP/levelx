<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalesHistory extends Model
{
    use HasFactory;

    public $timestamps = false; // We use 'recorded_at' manually or default

    protected $table = 'product_sales_history';

    protected $fillable = [
        'product_id',
        'quantity',
        'revenue',
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
