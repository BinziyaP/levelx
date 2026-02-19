@extends('layouts.seller')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium mb-4">Seller Dashboard</h3>

    <div class="mt-4">
        <!-- 1. Seller Analytics -->
        <h4 class="text-gray-600 font-medium text-lg mb-4">Performance Overview</h4>
        <div class="flex flex-wrap -mx-6 mb-8">
             <!-- Revenue -->
            <div class="w-full px-6 sm:w-1/2 xl:w-1/4">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-green-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">₹{{ number_format($sellerRevenue, 0) }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Units Sold -->
            <div class="w-full px-6 sm:w-1/2 xl:w-1/4 mt-4 sm:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-blue-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ number_format($sellerUnitsSold) }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Units Sold</div>
                    </div>
                </div>
            </div>

            <!-- Avg Rating -->
            <div class="w-full px-6 sm:w-1/2 xl:w-1/4 mt-4 xl:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75 text-white">
                         <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ number_format($sellerAvgRating, 1) }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Avg Rating</div>
                    </div>
                </div>
            </div>

             <!-- Total Reviews -->
            <div class="w-full px-6 sm:w-1/2 xl:w-1/4 mt-4 xl:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-purple-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ number_format($sellerTotalReviews) }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Total Reviews</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Product Stats -->
        <h4 class="text-gray-600 font-medium text-lg mb-4">Product Inventory</h4>
        <div class="flex flex-wrap -mx-6">
            <div class="w-full px-6 sm:w-1/2 xl:w-1/4">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75 text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $totalProducts }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Total Products</div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 sm:w-1/2 xl:w-1/4 mt-4 sm:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-green-600 bg-opacity-75 text-white">
                         <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $approvedProducts }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Approved</div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 sm:w-1/2 xl:w-1/4 mt-4 xl:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-yellow-600 bg-opacity-75 text-white">
                         <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $pendingProducts }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Pending</div>
                    </div>
                </div>
            </div>

             <div class="w-full px-6 sm:w-1/2 xl:w-1/4 mt-4 xl:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white dark:bg-gray-800">
                    <div class="p-3 rounded-full bg-red-600 bg-opacity-75 text-white">
                         <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $declinedProducts }}</h4>
                        <div class="text-gray-500 dark:text-gray-400">Declined</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-between items-center">
        <h3 class="text-gray-700 text-2xl font-medium">Recent Orders</h3>
        <a href="{{ route('seller.orders') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View All Orders &rarr;</a>
    </div>

    <div class="mt-4 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
             @forelse($notifications->take(5) as $notification)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $notification->data['product_name'] ?? 'Product' }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->data['buyer_name'] ?? 'Buyer' }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <p>
                                    ₹{{ $notification->data['amount'] ?? '0' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li>
                    <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
                        No recent orders.
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <a href="{{ route('products.create') }}" class="block p-6 border rounded-lg hover:bg-white dark:hover:bg-gray-800 bg-gray-50 dark:bg-gray-700 transition">
            <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mb-2">Add New Product</h3>
            <p class="text-gray-600 dark:text-gray-300">Listing a new product for sale.</p>
        </a>

        <a href="{{ route('products.index') }}" class="block p-6 border rounded-lg hover:bg-white dark:hover:bg-gray-800 bg-gray-50 dark:bg-gray-700 transition">
            <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mb-2">View My Products</h3>
            <p class="text-gray-600 dark:text-gray-300">Manage your existing product listings.</p>
        </a>
    </div>
@endsection
