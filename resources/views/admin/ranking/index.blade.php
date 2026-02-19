@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium">Smart Product Ranking Settings</h3>
    
    <div class="mt-8">
        <div class="p-6 bg-white rounded-md shadow-md">
            <h4 class="text-xl text-gray-600 font-semibold mb-4">Ranking Algorithm Weights</h4>
            <p class="text-gray-500 mb-6">
                Adjust the weights to control how products are ranked. 
                <br>
                <strong>Formula:</strong> 
                <code class="bg-gray-100 p-1 rounded">Score = (Sales × W1) + (Avg Rating × W2) - (Return Rate × W3)</code>
            </p>

            <form action="{{ route('admin.ranking-settings.update') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Sales Weight -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-2">Sales Weight (W1)</label>
                        <input type="number" step="0.1" name="sales_weight" value="{{ $settings->sales_weight ?? 1.0 }}" 
                               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-600">
                        <p class="text-xs text-gray-500 mt-1">Higher value ranks best-selling items higher.</p>
                    </div>

                    <!-- Rating Weight -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-2">Rating Weight (W2)</label>
                        <input type="number" step="0.1" name="rating_weight" value="{{ $settings->rating_weight ?? 1.0 }}" 
                               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-600">
                        <p class="text-xs text-gray-500 mt-1">Higher value ranks highly-rated items higher.</p>
                    </div>

                    <!-- Return Rate Weight -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-2">Return Weight (W3)</label>
                        <input type="number" step="0.1" name="return_weight" value="{{ $settings->return_weight ?? 1.0 }}" 
                               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-600">
                        <p class="text-xs text-gray-500 mt-1">Higher value penalizes returned items more.</p>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <h4 class="text-xl text-gray-600 font-semibold mb-4">Best Seller Criteria</h4>
                <p class="text-gray-500 mb-6">
                    Define the threshold for a product to automatically receive the "Best Seller" badge.
                    <br>
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Badge Trigger: (Total Sales >= Min Sales) OR (Rating >= Min Rating)</span>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Min Sales -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-2">Minimum Sales</label>
                        <input type="number" name="min_sales_for_best_seller" value="{{ $settings->min_sales_for_best_seller ?? 100 }}" 
                               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-600">
                        <p class="text-xs text-gray-500 mt-1">Products with sales above this count get the badge.</p>
                    </div>

                    <!-- Min Rating -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-2">Minimum Rating</label>
                        <input type="number" step="0.1" max="5" name="min_rating_for_best_seller" value="{{ $settings->min_rating_for_best_seller ?? 4.5 }}" 
                               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-600">
                        <p class="text-xs text-gray-500 mt-1">Products with rating above this value get the badge.</p>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <div class="flex items-center justify-between bg-purple-50 p-4 rounded-lg border border-purple-100">
                    <div>
                        <h4 class="text-lg font-semibold text-purple-900">Temporary Early Review Mode</h4>
                        <p class="text-sm text-purple-700">Allow customers to review products immediately after purchase (confirmed/paid), even before delivery. Useful for gathering initial feedback.</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="allow_early_reviews" value="1" {{ ($settings->allow_early_reviews ?? false) ? 'checked' : '' }} class="form-checkbox h-6 w-6 text-purple-600">
                        <span class="ml-2 text-sm text-gray-700">Enable</span>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2 leading-5 text-white transition-colors duration-200 transform bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:bg-indigo-700">
                        Save & Recalculate Rankings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
