@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('My Wishlist') }}
    </h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        @if($wishlists->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($wishlists as $item)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-4 relative group">
                        @if($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-48 object-cover rounded-md mb-4 bg-white">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-600 rounded-md mb-4 flex items-center justify-center">
                                <span class="text-gray-500 dark:text-gray-400">No Image</span>
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold">{{ $item->product->name }}</h3>
                        <p class="text-indigo-600 font-bold mt-1">₹{{ $item->product->price }}</p>
                        
                        <div class="mt-4 flex space-x-2">
                             <form action="{{ route('cart.add', $item->product->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded transition-colors text-sm">
                                    Add to Cart
                                </button>
                            </form>
                            <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove from wishlist?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded transition-colors" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <p class="text-gray-500 text-lg">Your wishlist is empty.</p>
                <a href="{{ route('shop') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
