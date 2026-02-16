@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Shop Products') }}
    </h2>
@endsection

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    <!-- Sidebar Filters -->
    <div class="w-full md:w-1/4">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 sticky top-24">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filter Products</h3>
            <form action="{{ route('shop') }}" method="GET">
                <!-- Search -->
                <div class="mb-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Product or Brand..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price Range</label>
                    <div class="flex items-center space-x-2 mt-1">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                        <span class="text-gray-500">-</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                    </div>
                </div>

                <!-- Sort By -->
                <div class="mb-4">
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                    <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                        Apply
                    </button>
                    <a href="{{ route('shop') }}" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-center text-sm font-medium transition-colors dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="w-full md:w-3/4">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden group flex flex-col h-full">
                            
                            <!-- Image Container -->
                            <div class="relative w-full pb-[100%] bg-white p-4 overflow-hidden border-b border-gray-50 dark:border-gray-700">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="absolute top-0 left-0 w-full h-full object-contain p-2 transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                                        <span class="text-gray-400 dark:text-gray-500 text-sm">No Image</span>
                                    </div>
                                @endif
                                
                                <!-- Hover Quick Actions (Optional) -->
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col space-y-2">
                                     <form action="{{ route('wishlist.store', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-white dark:bg-gray-800 text-gray-400 hover:text-red-500 p-2 rounded-full shadow-md border border-gray-100 dark:border-gray-600 transition-colors" title="Add to Wishlist">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="mb-2">
                                    <div class="flex items-center space-x-2 text-xs mb-1">
                                        @if($product->brand)
                                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold">{{ $product->brand }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug line-clamp-2 h-10 group-hover:text-indigo-600 transition-colors">{{ $product->name }}</h3>
                                </div>
                                
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-2 h-10">{{ Str::limit($product->description, 80) }}</p>
                                
                                <div class="mt-auto pt-4 border-t border-gray-50 dark:border-gray-700 flex justify-between items-center">
                                    <div>
                                         <span class="text-lg font-bold text-gray-900 dark:text-white">₹{{ number_format($product->price, 0) }}</span>
                                    </div>
                                    
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Add
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-1"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters.</p>
                            <div class="mt-6">
                                <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Clear Filters
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
