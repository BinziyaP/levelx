@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Shop Products') }}
    </h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow duration-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 relative group">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-md mb-4 bg-white">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-600 rounded-md mb-4 flex items-center justify-center">
                            <span class="text-gray-500 dark:text-gray-400">No Image</span>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold mb-2 text-indigo-600 dark:text-indigo-400">{{ $product->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 h-20 overflow-hidden">{{ Str::limit($product->description, 100) }}</p>
                    
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-100">₹{{ number_format($product->price, 2) }}</span>
                        
                        <div class="flex space-x-2">
                            <form action="{{ route('wishlist.store', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-2" title="Add to Wishlist">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </form>
                            
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-10">
                    <p class="text-gray-500 text-lg">No products available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
