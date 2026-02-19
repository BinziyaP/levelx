<?php

namespace App\Listeners;

use App\Events\ReviewSubmitted;
use App\Models\ProductRatingHistory;
use App\Services\ProductRankingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class HandleReviewSubmission
{
    public function handle(ReviewSubmitted $event)
    {
        $review = $event->review;
        $product = $review->product;

        // 1. Recalculate Aggregates
        // We use a direct query for accuracy
        $aggregates = $product->reviews()
            ->selectRaw('count(*) as total, avg(rating) as average')
            ->first();

        $totalReviews = $aggregates->total;
        $avgRating = $aggregates->average; // This returns existing records + new one

        // 2. Update Product
        $product->update([
            'total_reviews' => $totalReviews,
            'average_rating' => $avgRating,
            'avg_rating' => $avgRating // Update old column too for compatibility
        ]);

        // 3. Record History Snapshot
        ProductRatingHistory::create([
            'product_id' => $product->id,
            'average_rating' => $avgRating,
            'total_reviews' => $totalReviews,
            'recorded_at' => now(),
        ]);

        // 4. Update Best Seller Status
        $product->determineBestSellerStatus();

        // 5. Recalculate Ranking Score (so dashboard reflects new rating immediately)
        $rankingService = app(ProductRankingService::class);
        $rankingService->updateProductStats($product->id);
    }
}
