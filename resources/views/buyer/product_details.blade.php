@extends('layouts.app')

@section('content')
<div class="bg-gray-100 dark:bg-gray-900 min-h-screen py-10">
    <div class="container mx-auto px-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Product Image -->
                <div class="md:w-1/2 p-4">
                    <img class="w-full h-96 object-contain bg-gray-50 dark:bg-gray-700 rounded-md" 
                         src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}">
                </div>

                <!-- Product Details -->
                <div class="md:w-1/2 p-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $product->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Brand: {{ $product->brand }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Category: {{ $product->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        @if($product->is_best_seller)
                            <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                🏆 Best Seller
                            </span>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center">
                        <div class="flex items-center text-yellow-400">
                             @for($i=1; $i<=5; $i++)
                                <svg class="h-5 w-5 fill-current {{ $i <= round($product->avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                             @endfor
                        </div>
                        <span class="text-gray-600 dark:text-gray-300 ml-2 text-sm">{{ number_format($product->avg_rating, 1) }} ({{ $product->total_reviews }} reviews)</span>
                    </div>

                    <p class="mt-4 text-gray-600 dark:text-gray-300">{{ $product->description }}</p>

                    <div class="mt-6">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">₹{{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-indigo-700 transition">
                                Add to Cart
                            </button>
                        </form>
                        <!-- Wishlist button could go here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Ratings Section -->
        <div id="reviews" class="mt-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Customer Ratings</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Rating Summary -->
                <div class="md:col-span-1 border-r border-gray-200 dark:border-gray-700 pr-8">
                    <div class="text-5xl font-bold text-gray-900 dark:text-white mb-2">{{ number_format($product->avg_rating, 1) }}</div>
                    <div class="text-gray-500 mb-6">Out of 5</div>
                    
                    @foreach([5,4,3,2,1] as $star)
                        <div class="flex items-center mb-2">
                            <span class="text-sm text-gray-600 w-3">{{ $star }}</span>
                            <span class="text-yellow-400 ml-1">★</span>
                            <div class="flex-1 h-2 mx-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-yellow-400 rounded-full" style="width: {{ $totalReviews > 0 ? ($ratings[$star] / $totalReviews) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm text-gray-500 w-6 text-right">{{ $ratings[$star] }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Ratings List & Form -->
                <div class="md:col-span-2">
                    @if($canReview)
                        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-bold text-lg mb-2">Rate this Product</h4>
                            <form action="{{ route('products.review.store', $product->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Rating</label>
                                    <div class="flex items-center space-x-4">
                                        <select name="rating" class="border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="5">★★★★★ (Excellent)</option>
                                            <option value="4">★★★★☆ (Good)</option>
                                            <option value="3">★★★☆☆ (Average)</option>
                                            <option value="2">★★☆☆☆ (Poor)</option>
                                            <option value="1">★☆☆☆☆ (Terrible)</option>
                                        </select>
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">Submit Rating</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse($product->reviews as $review)
                            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center space-x-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</div>
                                    <div class="flex items-center text-yellow-400 text-sm">
                                        @for($i=1; $i<=5; $i++)
                                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">No ratings yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
