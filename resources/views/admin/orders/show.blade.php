@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-gray-700 text-3xl font-medium">Order #{{ $order->id }} Details</h3>
        <a href="{{ route('admin.buyers') }}" class="text-indigo-600 hover:text-indigo-900">Back to Orders</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Order Information -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Information</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm leading-5 font-medium text-gray-500">Buyer Name</dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900">{{ $order->user->name ?? 'Unknown' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm leading-5 font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900">₹{{ number_format($order->total_price, 2) }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm leading-5 font-medium text-gray-500">Global Status</dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900">{{ ucfirst($order->status) }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm leading-5 font-medium text-gray-500">Payment ID</dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900">{{ $order->payment_id }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm leading-5 font-medium text-gray-500">IP Address</dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900">{{ $order->ip_address ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm leading-5 font-medium text-gray-500">Ordered At</dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900">{{ $order->created_at->format('d M Y, h:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Fraud Analysis -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Fraud Analysis</h3>
                @if($order->is_suspicious)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Suspicious</span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Safe</span>
                @endif
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <span class="text-gray-700 font-medium mr-2">Total Fraud Score:</span>
                    <span class="text-xl font-bold {{ $order->fraud_score > 50 ? 'text-red-600' : 'text-green-600' }}">{{ $order->fraud_score }}</span>
                </div>

                @if($order->fraudLogs->count() > 0)
                    <h4 class="text-md font-medium text-gray-700 mb-2">Detailed Logs:</h4>
                    <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                        @foreach($order->fraudLogs as $log)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="ml-2 flex-1 w-0 truncate">
                                        {{ $log->message }}
                                        <span class="text-xs text-gray-500 block">Rule: {{ $log->rule->rule_name ?? 'Unknown' }}</span>
                                    </span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span class="font-bold text-red-600">+{{ $log->score_added }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No fraud rules matched for this order.</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
             <ul class="divide-y divide-gray-200">
                @foreach($order->items as $id => $item)
                    <li class="py-4 flex">
                       <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $item['name'] }}</p>
                            <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] }} | Price: ₹{{ $item['price'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
@endsection
