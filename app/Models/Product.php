<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand',
        'description',
        'price',
        'quantity',
        'image',
        'status',
        'total_sales',
        'avg_rating',
        'average_rating', // Add new column
        'total_reviews',  // Add new column
        'return_rate',
        'ranking_score',
        'is_best_seller', // Add new column
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function salesHistory()
    {
        return $this->hasMany(ProductSalesHistory::class);
    }

    public function ratingHistory()
    {
        return $this->hasMany(ProductRatingHistory::class);
    }

    /**
     * Determine and update Best Seller status based on settings.
     */
    public function determineBestSellerStatus()
    {
        $settings = \DB::table('ranking_settings')->first();
        if (!$settings) return;

        $minSales = $settings->min_sales_for_best_seller ?? 100;
        $minRating = $settings->min_rating_for_best_seller ?? 4.5;

        // Condition 1: High Sales
        $isHighSales = $this->total_sales >= $minSales;

        // Condition 2: High Rating (Ignore review count as per request)
        // Use average_rating if populated, else avg_rating fallback, ensure > 0 to avoid defaults
        $rating = $this->average_rating > 0 ? $this->average_rating : $this->avg_rating;
        $isHighRating = $rating >= $minRating && $this->total_reviews > 0; // Implicitly need at least 1 review to have a valid rating

        $isBestSeller = $isHighSales || $isHighRating;

        if ($this->is_best_seller !== $isBestSeller) {
            $this->update(['is_best_seller' => $isBestSeller]);
        }
        
        return $isBestSeller;
    }
}
