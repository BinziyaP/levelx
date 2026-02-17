@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium mb-6">Edit Fraud Rule</h3>

    <div class="w-full max-w-lg">
        <form action="{{ route('fraud-rules.update', $fraudRule) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rule_name">
                    Rule Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="rule_name" type="text" name="rule_name" value="{{ $fraudRule->rule_name }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rule_type">
                    Rule Type
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="rule_type" name="rule_type" required>
                    <option value="cart_value" {{ $fraudRule->rule_type == 'cart_value' ? 'selected' : '' }}>High Cart Value</option>
                    <option value="multiple_orders" {{ $fraudRule->rule_type == 'multiple_orders' ? 'selected' : '' }}>Multiple Orders (Time Based)</option>
                    <option value="same_ip" {{ $fraudRule->rule_type == 'same_ip' ? 'selected' : '' }}>Same IP Address</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="threshold_value">
                    Threshold Value
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="threshold_value" type="number" step="0.01" name="threshold_value" value="{{ $fraudRule->threshold_value }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="time_window_minutes">
                    Time Window (Minutes)
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="time_window_minutes" type="number" name="time_window_minutes" value="{{ $fraudRule->time_window_minutes }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="weight">
                    Risk Weight
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="weight" type="number" name="weight" value="{{ $fraudRule->weight }}" required>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" class="form-checkbox" {{ $fraudRule->is_active ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Rule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
