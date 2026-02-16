@extends('layouts.app')

@section('content')
<div class="relative bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-transparent sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Welcome to</span>
                        <span class="block text-indigo-600 xl:inline">eShopy</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 dark:text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        At eShopy, we believe technology should be accessible, reliable, and exciting. We’ve curated a collection of premium electronics that combine cutting-edge innovation with everyday utility. Whether you're building a custom PC or upgrading your smart home, we provide the tools you need to power your passion.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="{{ route('shop') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg transition duration-300 ease-in-out transform hover:scale-105">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 flex items-center justify-center p-8 lg:p-0">
        <!-- Interactive Electronics Composition -->
        <div class="relative w-full max-w-md lg:max-w-full h-64 sm:h-72 md:h-96 lg:h-full flex items-center justify-center">
            
            <!-- Custom Animation Styles -->
            <style>
                @keyframes float-slow {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-20px); }
                }
                @keyframes float-medium {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-15px); }
                }
                @keyframes float-fast {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-10px); }
                }
                .animate-float-slow { animation: float-slow 6s ease-in-out infinite; }
                .animate-float-medium { animation: float-medium 5s ease-in-out infinite; }
                .animate-float-fast { animation: float-fast 4s ease-in-out infinite; }
            </style>

            <!-- Background Decor -->
            <div class="absolute w-72 h-72 bg-indigo-300 dark:bg-indigo-900 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float-slow top-0 -left-4"></div>
            <div class="absolute w-72 h-72 bg-purple-300 dark:bg-purple-900 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float-medium bottom-0 -right-4"></div>

            <!-- Laptop Card (Top Right) -->
            <div class="absolute top-10 right-10 md:top-20 md:right-20 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 w-32 md:w-40 animate-float-slow transform hover:scale-110 transition duration-300 cursor-default">
                <div class="flex justify-center mb-2">
                    <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-center font-bold text-gray-800 dark:text-gray-200 text-sm">Laptops</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 mt-2 rounded-full overflow-hidden">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 70%"></div>
                </div>
            </div>

            <!-- Phone Card (Bottom Left) -->
            <div class="absolute bottom-10 left-10 md:bottom-20 md:left-20 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 w-28 md:w-36 animate-float-medium transform hover:scale-110 transition duration-300 cursor-default">
                <div class="flex justify-center mb-2">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-center font-bold text-gray-800 dark:text-gray-200 text-sm">Phones</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 mt-2 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: 85%"></div>
                </div>
            </div>

            <!-- Watch Card (Center) -->
            <div class="absolute bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-40 md:w-48 animate-float-fast transform hover:scale-110 transition duration-300 z-10 cursor-default">
                <div class="flex justify-center mb-3">
                    <svg class="w-12 h-12 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-center font-bold text-gray-900 dark:text-white text-lg">Gadgets</p>
                <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-1">New Arrivals</p>
                <button class="mt-3 w-full py-1.5 bg-gray-900 dark:bg-indigo-600 text-white text-xs font-bold rounded hover:bg-gray-700 transition">Explore</button>
            </div>
            
             <!-- Headphone Icon (Floating Small) -->
             <div class="absolute top-1/4 left-1/4 animate-float-fast hidden sm:block">
                <div class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-md text-blue-500">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
             </div>

        </div>
    </div>
</div>

<!-- About Us Section -->
<div id="about" class="bg-white dark:bg-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">About Us</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Who We Are
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto leading-relaxed">
                We are a passionate team dedicated to providing the best shopping experience. Our mission is to connect buyers with high-quality products from trusted sellers, ensuring a seamless and secure transaction every time.
            </p>
        </div>
    </div>
</div>

<!-- Mission & Vision Section -->
<div class="bg-gray-50 dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Mission Card -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-indigo-500">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Our Mission</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    To empower millions of buyers and sellers around the world, providing comprehensive opportunities for everyone. We aim to build a future where commerce is accessible, inclusive, and sustainable for all.
                </p>
            </div>

            <!-- Vision Card -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-indigo-500">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Our Vision</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    To be the Earth's most customer-centric company, where customers can find and discover anything they might want to buy online. We strive to set the global standard for online retail excellence.
                </p>
            </div>
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group">
                    @if($product->image)
                        <div class="overflow-hidden">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover transform group-hover:scale-110 transition duration-500">
                        </div>
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <span class="text-gray-500 dark:text-gray-400">No Image</span>
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                        <p class="text-indigo-600 font-bold text-xl">₹{{ $product->price }}</p>
                        <a href="{{ route('shop') }}" class="mt-4 block w-full text-center bg-gray-900 text-white py-2 rounded-md hover:bg-indigo-600 transition-colors duration-300 shadow-md">View Details</a>
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
