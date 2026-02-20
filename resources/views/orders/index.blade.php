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
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'returned' => 'bg-purple-100 text-purple-800',
                                                'refunded' => 'bg-red-100 text-red-800',
                                            ];
                                            $colorClass = $statusColors[$order->shipping_status] ?? 'bg-blue-100 text-blue-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                            {{ ucfirst($order->shipping_status) }}
                                        </span>
                                    @endif

                                    {{-- Track Order Button --}}
                                    @if(!$order->is_suspicious && in_array($order->shipping_status, ['packed', 'shipped', 'delivered']))
                                        <a href="{{ route('orders.track', $order->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Track Order
                                        </a>
                                    @endif

                                    {{-- Unified Professional Return/Dispute Logic --}}
                                    @if($order->returnRequest)
                                        <div class="flex items-center space-x-2">
                                            @php
                                                $status = $order->returnRequest->status;
                                                $color = match($status) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'under_review' => 'bg-blue-100 text-blue-800',
                                                    'resolved' => 'bg-green-100 text-green-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center text-xs leading-5 font-semibold rounded-full px-2 py-1 {{ $color }}">
                                                🔄 Return: {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>

                                            @if($status === 'resolved' && $order->returnRequest->resolution_type)
                                                <div class="mt-1 flex items-center gap-2">
                                                    <span class="text-[10px] font-black uppercase tracking-tight {{ $order->returnRequest->resolution_type === 'full_refund' ? 'text-green-600' : 'text-indigo-600' }}">
                                                        {{ $order->returnRequest->resolution_type === 'full_refund' ? '✅ Full Refund' : '💰 Partial Refund' }}:
                                                        ₹{{ number_format($order->returnRequest->refund_amount, 2) }}
                                                    </span>
                                                </div>
                                            @endif


                                            @if($status === 'resolved' && $order->returnRequest->refund_amount > 0 && !$order->returnRequest->razorpay_refund_id)
                                                <button 
                                                    onclick="startRefundClaim({{ $order->id }}, {{ $order->returnRequest->refund_amount }})"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-[10px] font-bold rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none shadow-sm transition-all"
                                                >
                                                    💰 Get Refund
                                                </button>
                                            @endif
                                        </div>
                                    @elseif(!$order->is_suspicious && $order->shipping_status === 'delivered')
                                        {{-- Return Order Button (Universal for Cancel/Return) --}}
                                        <button type="button" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.remove('hidden')" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 focus:outline-none">
                                            🔄 Return Order
                                        </button>

                                        {{-- Return Modal --}}
                                        <div id="return-modal-{{ $order->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('return-modal-{{ $order->id }}').classList.add('hidden')"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                    <form action="{{ route('orders.return.store', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Return Order #{{ $order->id }}</h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">Please tell us why you want to return this order. <strong>You must upload photos as evidence.</strong></p>
                                                                
                                                                <div class="mt-4">
                                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select a Reason</label>
                                                                    <select name="reason_dropdown" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                                                                        <option value="">-- Select a reason --</option>
                                                                        <option value="Changed my mind">Changed my mind</option>
                                                                        <option value="Ordered by mistake">Ordered by mistake</option>
                                                                        <option value="Technical malfunction / Not working">Technical malfunction / Not working</option>
                                                                        <option value="Software / Performance issues">Software / Performance issues</option>
                                                                        <option value="Battery or Charging issue">Battery or Charging issue</option>
                                                                        <option value="Display / Screen problems">Display / Screen problems</option>
                                                                        <option value="Wrong item received">Wrong item received</option>
                                                                        <option value="Product damaged or defective">Product damaged or defective</option>
                                                                        <option value="other">Other</option>
                                                                    </select>
                                                                </div>

                                                                <div class="mt-3">
                                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Evidence Photos</label>
                                                                    <input type="file" name="evidences[]" required multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
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

{{-- Razorpay Style Refund Animation Modal --}}
<div id="refund-animation-modal" class="fixed inset-0 z-[60] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm transition-opacity"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-indigo-100 dark:border-gray-700">
            <div class="p-8 text-center">
                <!-- Header -->
                <div class="flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="currentColor" class="w-12 h-12 text-indigo-600">
                        <path d="M50 20 A30 30 0 1 0 80 50 H50 V40 H90 A40 40 0 1 1 50 10 C35 10 25 15 15 25 L25 35 C32 25 40 20 50 20 Z" />
                        <path d="M15 25 L5 15" stroke="currentColor" stroke-width="10" stroke-linecap="round" />
                        <circle cx="35" cy="85" r="6" />
                        <circle cx="65" cy="85" r="6" />
                    </svg>
                    <span class="ml-3 text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">eShopy</span>
                </div>

                <!-- Progress Stages -->
                <div id="refund-stages" class="space-y-6 relative">
                    <!-- Step 1: Processing -->
                    <div id="step-processing" class="flex flex-col items-center">
                        <div class="w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">Processing Refund</h4>
                        <p class="text-sm text-gray-500 mt-1">Initiating secure transfer for <span class="font-bold text-indigo-600" id="anim-amount">₹0.00</span></p>
                    </div>

                    <!-- Step 2: Crediting (Hidden initially) -->
                    <div id="step-crediting" class="hidden flex flex-col items-center animate-pulse">
                        <div class="relative w-24 h-24 mb-4">
                            <div class="absolute inset-0 bg-green-100 dark:bg-green-900/30 rounded-full animate-ping"></div>
                            <div class="relative w-24 h-24 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-black text-green-600">Crediting Back...</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Connecting to original payment method</p>
                    </div>

                    <!-- Step 3: Success (Hidden initially) -->
                    <div id="step-success" class="hidden flex flex-col items-center">
                        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center mb-4 transition-all duration-500 scale-110 shadow-inner">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 class="text-2xl font-black text-gray-900 dark:text-gray-100">Refund Success!</h4>
                        <p class="text-sm text-gray-500 mt-2">The amount has been successfully credited.</p>
                        <button onclick="window.location.reload()" class="mt-8 w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-black transition-colors">Done</button>
                    </div>
                </div>

                <!-- Footer Badge -->
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-center gap-2 opacity-60">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 italic">Secured by</span>
                    <div class="flex items-center gap-1 text-indigo-600 font-black italic">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/></svg> 
                        <span>Razorpay</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    @push('scripts')
        <script>
            function startRefundClaim(orderId, amount) {
                const modal = document.getElementById('refund-animation-modal');
                const animAmount = document.getElementById('anim-amount');
                const stepProcessing = document.getElementById('step-processing');
                const stepCrediting = document.getElementById('step-crediting');
                const stepSuccess = document.getElementById('step-success');

                if (!modal || !animAmount) {
                    console.error('Refund modal elements not found');
                    return;
                }

                animAmount.innerText = '₹' + amount.toLocaleString('en-IN', {minimumFractionDigits: 2});
                modal.classList.remove('hidden');

                // Show processing for 1s
                setTimeout(() => {
                    stepProcessing.classList.add('hidden');
                    stepCrediting.classList.remove('hidden');

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    // Trigger Backend API simultaneously
                    fetch(`/orders/${orderId}/claim-refund`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show Success after 2s of "Crediting" animation
                            setTimeout(() => {
                                stepCrediting.classList.add('hidden');
                                stepSuccess.classList.remove('hidden');
                            }, 2000);
                        } else {
                            alert('Error: ' + data.message);
                            modal.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An unexpected error occurred.');
                        modal.classList.add('hidden');
                    });

                }, 1000);
            }
        </script>
    @endpush
@endsection
