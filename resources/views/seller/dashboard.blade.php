@extends('layouts.seller')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium mb-4">Seller Dashboard</h3>

    <div class="mt-4">
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
