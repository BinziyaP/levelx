@extends('layouts.app')

@section('content')
<div class="relative bg-white dark:bg-gray-800 overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white dark:bg-gray-800 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Welcome to</span>
                        <span class="block text-indigo-600 xl:inline">E-Shop</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 dark:text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Your one-stop destination for all your needs. Browse our extensive collection of products and enjoy seamless shopping.
                    </p>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- About Us Section -->
<div id="about" class="bg-gray-50 dark:bg-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">About Us</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Who We Are
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
                We are a passionate team dedicated to providing the best shopping experience. Our mission is to connect buyers with high-quality products from trusted sellers.
            </p>
        </div>

        <div class="mt-10">
            <!-- Content removed as per request -->
        </div>
    </div>
</div>

<div class="bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-8">Featured Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @php
                $products = \App\Models\Product::where('status', 'approved')
                    ->whereHas('user', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->take(4)->get();
            @endphp
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <span class="text-gray-500 dark:text-gray-400">No Image</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $product->name }}</h3>
                        <p class="text-indigo-600 mt-1">₹{{ $product->price }}</p>
                        <a href="{{ route('shop') }}" class="mt-4 block w-full text-center bg-gray-800 text-white py-2 rounded-md hover:bg-gray-700">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('shop') }}" class="text-indigo-600 hover:text-indigo-500 font-semibold">View All Products &rarr;</a>
        </div>
    </div>
</div>
@endsection
