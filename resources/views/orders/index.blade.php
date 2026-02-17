@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Order History') }}
    </h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <div>
                                <h3 class="font-bold text-lg">Order #{{ $order->id }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                @if($order->discount_amount > 0)
                                    <span class="block text-sm text-gray-500 line-through">₹{{ number_format($order->original_price, 2) }}</span>
                                    <span class="block text-xs text-green-600 font-semibold mb-1">Savings: ₹{{ number_format($order->discount_amount, 2) }}</span>
                                @endif
                                <span class="block text-xl font-bold text-indigo-600 dark:text-indigo-400">₹{{ number_format($order->total_price, 2) }}</span>
                                <div class="mt-2 space-x-2">
                                <div class="mt-2 space-x-2">
                                    @if($order->is_suspicious)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            ⚠️ Under Review
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->shipping_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($order->shipping_status) }}
                                        </span>
                                        @if(in_array($order->shipping_status, ['packed', 'shipped', 'delivered']))
                                            <a href="{{ route('orders.track', $order->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Track Order
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium mb-2">Items:</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300">
                                @if(is_array($order->items))
                                    @foreach($order->items as $item)
                                        <li>
                                            {{ $item['name'] }} x {{ $item['quantity'] }} - ₹{{ number_format($item['price'], 2) }}
                                        </li>
                                    @endforeach
                                @else
                                    <li>Order details not available.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-gray-500 text-lg">You have placed no orders yet.</p>
                <a href="{{ route('shop') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
