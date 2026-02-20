@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Raise Return — Order #{{ $order->id }}
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

            {{-- Order Summary --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <h3 class="font-bold text-lg mb-2">Order Summary</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Order ID:</span> #{{ $order->id }}</div>
                    <div><span class="text-gray-500">Total:</span> ₹{{ number_format($order->total_price, 2) }}</div>
                    <div><span class="text-gray-500">Date:</span> {{ $order->created_at->format('d M Y') }}</div>
                    <div><span class="text-gray-500">Status:</span> {{ ucfirst($order->shipping_status) }}</div>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form action="{{ route('disputes.store', $order->id) }}" method="POST" enctype="multipart/form-data" id="dispute-form">
                @csrf

                {{-- Reason Dropdown --}}
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Return</label>
                    <select name="reason" id="reason" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <option value="">— Select a reason —</option>
                        <option value="Product damaged or defective" {{ old('reason') == 'Product damaged or defective' ? 'selected' : '' }}>Product damaged or defective</option>
                        <option value="Wrong item received" {{ old('reason') == 'Wrong item received' ? 'selected' : '' }}>Wrong item received</option>
                        <option value="Product not as described" {{ old('reason') == 'Product not as described' ? 'selected' : '' }}>Product not as described</option>
                        <option value="Item missing from order" {{ old('reason') == 'Item missing from order' ? 'selected' : '' }}>Item missing from order</option>
                        <option value="Quality not satisfactory" {{ old('reason') == 'Quality not satisfactory' ? 'selected' : '' }}>Quality not satisfactory</option>
                        <option value="Counterfeit or fake product" {{ old('reason') == 'Counterfeit or fake product' ? 'selected' : '' }}>Counterfeit or fake product</option>
                        <option value="Seller not responding" {{ old('reason') == 'Seller not responding' ? 'selected' : '' }}>Seller not responding</option>
                        <option value="Other" {{ old('reason') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Describe the Issue</label>
                    <textarea name="description" id="description" rows="5" required maxlength="2000" placeholder="Please provide as much detail as possible about your issue..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Maximum 2000 characters</p>
                </div>

                {{-- Evidence Upload --}}
                <div class="mb-6">
                    <label for="evidence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Evidence</label>
                    <input type="file" name="evidence[]" id="evidence" multiple accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                        file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100 dark:file:bg-gray-600 dark:file:text-gray-200">
                    <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF. Max 5MB per file.</p>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Back to Orders</a>
                    <button type="submit" id="submit-btn" onclick="this.disabled=true; this.innerText='Submitting...'; document.getElementById('dispute-form').submit();" class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Submit Return Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
