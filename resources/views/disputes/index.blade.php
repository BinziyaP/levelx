<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Returns') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Your Return Cases</h3>
                    
                    @if($returns->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">You haven't raised any returns yet.</p>
                            <a href="{{ route('orders.index') }}" class="mt-4 inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                View your orders to raise a return &rarr;
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolution</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($returns as $return)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                #RET-{{ $return->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Order #{{ $return->order->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $color = match($return->status) {
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'under_review' => 'bg-blue-100 text-blue-800',
                                                        'resolved' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    };
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                    {{ ucfirst(str_replace('_', ' ', $return->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($return->resolution_type)
                                                    @php
                                                        $resColor = match($return->resolution_type) {
                                                            'full_refund' => 'bg-green-100 text-green-800',
                                                            'partial_refund' => 'bg-indigo-100 text-indigo-800',
                                                            default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    @endphp
                                                    <div class="flex flex-col">
                                                        <span class="px-2 inline-flex text-[10px] leading-4 font-bold uppercase rounded-full {{ $resColor }} w-fit">
                                                            {{ str_replace('_', ' ', $return->resolution_type) }}
                                                        </span>
                                                        <span class="text-sm font-medium mt-1 text-gray-900 dark:text-gray-100">₹{{ number_format($return->refund_amount, 2) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $return->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                                <a href="{{ route('disputes.show', $return->id) }}" class="text-indigo-600 hover:text-indigo-900">Details</a>
                                                @if($return->status === 'resolved' && $return->refund_amount > 0 && !$return->razorpay_refund_id)
                                                    <button 
                                                        onclick="startRefundClaim({{ $return->order->id }}, {{ $return->refund_amount }})"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-bold rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none shadow-sm transition-all"
                                                    >
                                                        💰 Get Refund
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $returns->links() }}
                        </div>
                    @endif
                </div>

                <div id="raise-dispute" class="mt-12 border-t pt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Start a New Return</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        To initiate a return, please visit your <a href="{{ route('orders.index') }}" class="text-indigo-600 font-medium">Order History</a> and click on the "Return Order" button for the respective delivered order.
                    </p>
                </div>

            </div>
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

    <script>
        function startRefundClaim(orderId, amount) {
            const modal = document.getElementById('refund-animation-modal');
            const animAmount = document.getElementById('anim-amount');
            const stepProcessing = document.getElementById('step-processing');
            const stepCrediting = document.getElementById('step-crediting');
            const stepSuccess = document.getElementById('step-success');

            animAmount.innerText = '₹' + amount.toLocaleString('en-IN', {minimumFractionDigits: 2});
            modal.classList.remove('hidden');

            // Show processing for 1.5s
            setTimeout(() => {
                stepProcessing.classList.add('hidden');
                stepCrediting.classList.remove('hidden');

                // Trigger Backend API simultaneously
                fetch(`/orders/${orderId}/claim-refund`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
</x-app-layout>
