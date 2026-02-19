<?php

namespace App\Events;

use App\Models\ProductReview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
    }
}
