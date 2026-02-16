@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Track Order') }} #{{ $order->id }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Shipping Details</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Status</p>
                            <p class="mt-1 text-lg font-semibold capitalize">{{ $order->shipping_status }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Courier</p>
                            <p class="mt-1 text-lg">{{ config('couriers.'.$order->courier_code) ?? $order->courier_code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tracking Number</p>
                            <p class="mt-1 text-lg">{{ $order->tracking_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

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
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 {{ $index === 0 ? 'bg-green-500' : 'bg-gray-400' }}">
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
                </div>
                
                <div class="mt-6">
                     <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Back to Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
