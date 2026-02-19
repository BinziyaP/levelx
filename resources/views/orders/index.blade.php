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
                                    {{-- Status Badge --}}
                                    @if($order->is_suspicious)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            ⚠️ Under Review
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->shipping_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->shipping_status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst($order->shipping_status) }}
                                        </span>
                                    @endif

                                    {{-- Track Order Button --}}
                                    @if(!$order->is_suspicious && in_array($order->shipping_status, ['packed', 'shipped', 'delivered']))
                                        <a href="{{ route('orders.track', $order->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Track Order
                                        </a>
                                    @endif

                                    {{-- Cancel Order Button (for pending orders, not yet returned) --}}
                                    @if(in_array($order->status, ['pending', 'paid']) && !$order->returnRequest)
                                        <button type="button" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.remove('hidden')" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none">
                                            Cancel Order
                                        </button>

                                        {{-- Cancel Modal --}}
                                        <div id="return-modal-{{ $order->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.add('hidden')"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                    <form action="{{ route('orders.return.store', $order->id) }}" method="POST">
                                                        @csrf
                                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Cancel Order #{{ $order->id }}</h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to cancel this order?</p>
                                                                <div class="mt-4">
                                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select a Reason</label>
                                                                    <select onchange="var other=this.closest('form').querySelector('.cancel-other'); if(this.value==='other'){other.classList.remove('hidden');other.querySelector('textarea').required=true;}else{other.classList.add('hidden');other.querySelector('textarea').required=false;}" name="reason" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                                                                        <option value="">-- Select a reason --</option>
                                                                        <option value="Changed my mind">Changed my mind</option>
                                                                        <option value="Found a better price elsewhere">Found a better price elsewhere</option>
                                                                        <option value="Ordered by mistake">Ordered by mistake</option>
                                                                        <option value="Delivery time too long">Delivery time too long</option>
                                                                        <option value="Duplicate order">Duplicate order</option>
                                                                        <option value="other">Other (specify below)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mt-3 cancel-other hidden">
                                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Reason</label>
                                                                    <textarea name="custom_reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600" placeholder="Please describe your reason..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                                                Confirm Cancellation
                                                            </button>
                                                            <button type="button" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                Close
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    {{-- Return Order Button (for delivered orders) --}}
                                    @elseif(in_array($order->shipping_status, ['delivered']) && !$order->returnRequest)
                                        <button type="button" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.remove('hidden')" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 focus:outline-none">
                                            🔄 Return Order
                                        </button>

                                        {{-- Return Modal --}}
                                        <div id="return-modal-{{ $order->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.add('hidden')"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                    <form action="{{ route('orders.return.store', $order->id) }}" method="POST">
                                                        @csrf
                                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Return Order #{{ $order->id }}</h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">Please tell us why you want to return this order. A refund of ₹{{ number_format($order->total_price, 2) }} will be processed after approval.</p>
                                                                <div class="mt-4">
                                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select a Reason</label>
                                                                    <select onchange="var other=this.closest('form').querySelector('.return-other'); if(this.value==='other'){other.classList.remove('hidden');other.querySelector('textarea').required=true;}else{other.classList.add('hidden');other.querySelector('textarea').required=false;}" name="reason" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                                                                        <option value="">-- Select a reason --</option>
                                                                        <option value="Product damaged or defective">Product damaged or defective</option>
                                                                        <option value="Wrong item received">Wrong item received</option>
                                                                        <option value="Product not as described">Product not as described</option>
                                                                        <option value="Quality not satisfactory">Quality not satisfactory</option>
                                                                        <option value="Size or fit issue">Size or fit issue</option>
                                                                        <option value="No longer needed">No longer needed</option>
                                                                        <option value="other">Other (specify below)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mt-3 return-other hidden">
                                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Reason</label>
                                                                    <textarea name="custom_reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600" placeholder="Please describe your reason..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                                                Submit Return Request
                                                            </button>
                                                            <button type="button" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                Close
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($order->returnRequest)
                                        @if($order->returnRequest->status === 'approved')
                                            {{-- Show Get Refund button when admin has approved --}}
                                            <form action="{{ route('orders.claim-refund', $order->id) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Claim refund of ₹{{ number_format($order->returnRequest->refund_amount, 2) }}?')" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                                    💰 Get Refund (₹{{ number_format($order->returnRequest->refund_amount, 2) }})
                                                </button>
                                            </form>
                                        @elseif($order->returnRequest->status === 'refunded')
                                            <span class="ml-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 px-2 py-1">
                                                ✅ Refunded (₹{{ number_format($order->returnRequest->refund_amount, 2) }})
                                            </span>
                                        @elseif($order->returnRequest->status === 'pending')
                                            <span class="ml-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 px-2 py-1">
                                                ⏳ Cancellation Pending
                                            </span>
                                        @elseif($order->returnRequest->status === 'rejected')
                                            <span class="ml-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 px-2 py-1">
                                                ❌ Request Rejected
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium mb-2">Items:</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300">
                                @if(is_array($order->items))
                                    @foreach($order->items as $key => $item)
                                        <li>
                                            @php
                                                $productId = $item['product_id'] ?? $item['id'] ?? $key;
                                            @endphp
                                            
                                            @if($productId)
                                                <a href="{{ route('product.show', $productId) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                                    {{ $item['name'] }}
                                                </a>
                                            @else
                                                {{ $item['name'] }}
                                            @endif
                                            x {{ $item['quantity'] }} - ₹{{ number_format($item['price'], 2) }}
                                            
                                            @if($productId)
                                                <a href="{{ route('product.show', $productId) }}#reviews" class="ml-4 text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-100 border border-indigo-200">
                                                    Rate Product
                                                </a>
                                            @endif
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
