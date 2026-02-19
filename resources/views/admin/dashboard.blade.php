@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium">Dashboard</h3>

    <!-- 1. Global Metrics -->
    <div class="mt-8">
        <h4 class="text-gray-600 font-medium text-lg mb-4">Platform Overview</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Total Revenue -->
            <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                <div class="p-3 rounded-full bg-green-600 bg-opacity-75 text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mx-5">
                    <h4 class="text-2xl font-semibold text-gray-700">₹{{ number_format($totalRevenue, 0) }}</h4>
                    <div class="text-gray-500 text-sm">Total Revenue</div>
                    @if($revenue30Days > 0)
                        <div class="text-green-600 text-xs mt-1">₹{{ number_format($revenue30Days, 0) }} (30d)</div>
                    @endif
                </div>
            </div>

            <!-- Total Units Sold -->
            <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                <div class="p-3 rounded-full bg-blue-600 bg-opacity-75 text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="mx-5">
                    <h4 class="text-2xl font-semibold text-gray-700">{{ number_format($totalUnitsSold) }}</h4>
                    <div class="text-gray-500 text-sm">Units Sold</div>
                </div>
            </div>

            <!-- Approx Platform Rating -->
            <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75 text-white">
                     <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div class="mx-5">
                    <h4 class="text-2xl font-semibold text-gray-700">{{ number_format($avgRating, 1) }}</h4>
                    <div class="text-gray-500 text-sm">Avg Platform Rating</div>
                </div>
            </div>

             <!-- Total Reviews -->
            <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                <div class="p-3 rounded-full bg-purple-600 bg-opacity-75 text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="mx-5">
                    <h4 class="text-2xl font-semibold text-gray-700">{{ number_format($totalReviews) }}</h4>
                    <div class="text-gray-500 text-sm">Total Reviews</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 2. Existing Metrics (Users & Products) - Keep them as they are useful context -->
    <div class="mt-8">
        <h4 class="text-gray-600 font-medium text-lg mb-4">Store Overview</h4>
        <div class="flex flex-wrap -mx-6">
            <div class="w-full px-6 sm:w-1/2 xl:w-1/3">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                    <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ \App\Models\User::count() }}</h4>
                        <div class="text-gray-500">Total Users</div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 sm:w-1/2 xl:w-1/3 mt-6 sm:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                    <div class="p-3 rounded-full bg-gray-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 001-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ \App\Models\Product::count() }}</h4>
                        <div class="text-gray-500">Total Products</div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 sm:w-1/2 xl:w-1/3 mt-6 xl:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                    <div class="p-3 rounded-full bg-red-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 10-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ \App\Models\Product::where('status', 'pending')->count() }}</h4>
                        <div class="text-gray-500">Pending Products</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Leaderboards -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Selling -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="font-semibold text-gray-700">Top 5 Best Selling Products</h4>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-100">
                    @forelse($topSellingProducts as $item)
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex items-center">
                                <span class="text-gray-500 font-bold mr-4 text-lg">#{{ $loop->iteration }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $item->product ? $item->product->name : 'Deleted Product' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->total_units }} units sold</p>
                                </div>
                            </div>
                            <span class="text-green-600 font-semibold text-sm">₹{{ number_format($item->total_revenue) }}</span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">No sales data yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Top Rated -->
        <div class="bg-white rounded-lg shadow-sm">
             <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="font-semibold text-gray-700">Top 5 Highest Rated Products</h4>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-100">
                    @forelse($topRatedProducts as $product)
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex items-center">
                                <span class="text-gray-500 font-bold mr-4 text-lg">#{{ $loop->iteration }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->total_reviews }} reviews</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="text-yellow-500 font-bold text-sm mr-1">{{ number_format($product->average_rating, 1) }}</span>
                                <svg class="h-4 w-4 text-yellow-500 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">No rated products yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <!-- 4. Full Ranked Product List -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h4 class="font-semibold text-gray-700 text-lg">All Products — Sorted by Ranking Score</h4>
                <span class="text-xs text-gray-400">Score = (Sales × W1) + (Rating × W2) − (ReturnRate × W3)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Rating</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Return Rate</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Best Seller</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($rankedProducts as $product)
                            <tr class="{{ $product->is_best_seller ? 'bg-yellow-50' : '' }}">
                                <td class="px-4 py-3 text-sm font-bold text-gray-600">#{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" class="h-8 w-8 rounded object-cover mr-3" alt="">
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $product->brand ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $product->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold {{ $product->ranking_score > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ number_format($product->ranking_score, 1) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($product->total_sales) }}</td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <span class="text-yellow-500">{{ number_format($product->average_rating, 1) }}</span>
                                    <span class="text-gray-400 text-xs">({{ $product->total_reviews }})</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right {{ $product->return_rate > 10 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ number_format($product->return_rate, 1) }}%
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($product->is_best_seller)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⭐ Yes</span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-sm text-gray-500 text-center">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h4 class="text-gray-700 font-medium">Quick Actions</h4>
        <div class="mt-4 flex space-x-4">
             <a href="{{ route('admin.sellers') }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-4 py-2 rounded-md">Manage Sellers</a>
             <a href="{{ route('admin.products') }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-4 py-2 rounded-md">Manage Products</a>
             <a href="{{ route('admin.ranking-settings.index') }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-4 py-2 rounded-md">Ranking Algorithm</a>
        </div>
    </div>
</div>
@endsection
