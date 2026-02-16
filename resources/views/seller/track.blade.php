@extends('layouts.seller')

@section('content')
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
            Tracking Details - Order #{{ $order->id }}
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Current Status and Shipment Timeline
        </p>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Current Status
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 capitalize">
                    {{ $order->shipping_status }}
                </dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Courier & Tracking ID
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ config('couriers.'.$order->courier_code) ?? $order->courier_code ?? 'N/A' }} - {{ $order->tracking_number ?? 'N/A' }}
                </dd>
            </div>
        </dl>
    </div>

    <div class="px-4 py-5 sm:p-6">
        <!-- Dynamic Tracking UI -->
        <div class="mt-8">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-6">Shipment Timeline</h3>

            @php
                $trackingData = $order->tracking_response['data'] ?? [];
                $checkpoints = $trackingData['Checkpoints'] ?? $trackingData['checkpoints'] ?? [];
            @endphp

            @if(isset($order->tracking_response['success']) && $order->tracking_response['success'] == true && count($checkpoints) > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($checkpoints as $index => $checkpoint)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 {{ $index === 0 ? 'bg-indigo-600' : 'bg-gray-400' }}">
                                                @if($index === 0)
                                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <span class="h-2.5 w-2.5 bg-white rounded-full"></span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $checkpoint['Activity'] ?? $checkpoint['message'] ?? 'Update' }}</p>
                                                @if(isset($checkpoint['Location']))
                                                    <p class="text-xs text-gray-500">{{ $checkpoint['Location'] }}</p>
                                                @elseif(isset($checkpoint['location']))
                                                    <p class="text-xs text-gray-500">{{ $checkpoint['location'] }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                <time datetime="{{ $checkpoint['Date'] ?? '' }} {{ $checkpoint['Time'] ?? '' }}">
                                                    {{ $checkpoint['Date'] ?? '' }} {{ $checkpoint['Time'] ?? '' }}
                                                    {{ $checkpoint['checkpoint_time'] ?? '' }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <!-- Fallback to Static Status if API data is missing -->
                <div class="rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Tracking information not yet available</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Real-time tracking details will appear here once the courier updates the status. Current internal status: <span class="font-bold">{{ ucfirst($order->shipping_status) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        <div class="mt-6">
             <a href="{{ route('seller.orders') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Back to Orders
            </a>
        </div>
    </div>
@endsection
