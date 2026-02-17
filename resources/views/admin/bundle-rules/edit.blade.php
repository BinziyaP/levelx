@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 dark:text-gray-200 text-3xl font-medium">Edit Bundle Rule</h3>
    
    <div class="mt-8">
        <form action="{{ route('bundle-rules.update', $rule->id) }}" method="POST" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                <div>
                    <label class="text-gray-700 dark:text-gray-200" for="name">Rule Name</label>
                    <input id="name" name="name" type="text" class="form-input w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', $rule->name) }}" required>
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200" for="type">Rule Type</label>
                    <select id="type" name="type" class="form-select w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="item_count" {{ old('type', $rule->type) == 'item_count' ? 'selected' : '' }}>Item Count (Buy X items)</option>
                        <option value="category_variety" {{ old('type', $rule->type) == 'category_variety' ? 'selected' : '' }}>Category Variety (Buy from X categories)</option>
                    </select>
                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200" for="min_items">Min Items / Categories</label>
                    <input id="min_items" name="min_items" type="number" min="1" class="form-input w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('min_items', $rule->min_items) }}" required>
                    @error('min_items') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200" for="max_items">Max Items (Optional for Category)</label>
                    <input id="max_items" name="max_items" type="number" min="1" class="form-input w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('max_items', $rule->max_items) }}">
                    @error('max_items') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200" for="discount_type">Discount Type</label>
                    <select id="discount_type" name="discount_type" class="form-select w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="percentage" {{ old('discount_type', $rule->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type', $rule->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                    @error('discount_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200" for="discount_value">Discount Value</label>
                    <input id="discount_value" name="discount_value" type="number" step="0.01" min="0" class="form-input w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('discount_value', $rule->discount_value) }}" required>
                    @error('discount_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-indigo-600" {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700 dark:text-gray-200">Is Active?</span>
                </label>
            </div>

            <div class="flex justify-end mt-6">
                <a href="{{ route('bundle-rules.index') }}" class="px-6 py-2 leading-5 text-gray-700 transition-colors duration-200 transform bg-white border border-gray-200 rounded-md hover:bg-gray-100 focus:outline-none focus:bg-gray-100 mr-2">Cancel</a>
                <button type="submit" class="px-6 py-2 leading-5 text-white transition-colors duration-200 transform bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:bg-indigo-600">Update Rule</button>
            </div>
        </form>
    </div>
</div>
@endsection
