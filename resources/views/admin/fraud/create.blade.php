@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium mb-6">Create Fraud Rule</h3>

    <div class="w-full max-w-lg">
        <form action="{{ route('fraud-rules.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rule_name">
                    Rule Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="rule_name" type="text" name="rule_name" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rule_type">
                    Rule Type
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="rule_type" name="rule_type" required>
                    <option value="cart_value">High Cart Value</option>
                    <option value="multiple_orders">Multiple Orders (Time Based)</option>
                    <option value="same_ip">Same IP Address</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="threshold_value">
                    Threshold Value
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="threshold_value" type="number" step="0.01" name="threshold_value" required>
                <p class="text-gray-600 text-xs italic mt-1">Amount for Cart Value, Count for others.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="time_window_minutes">
                    Time Window (Minutes)
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="time_window_minutes" type="number" name="time_window_minutes">
                <p class="text-gray-600 text-xs italic mt-1">Only for Multiple Orders rule.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="weight">
                    Risk Weight
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="weight" type="number" name="weight" required>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" checked class="form-checkbox">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Create Rule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
