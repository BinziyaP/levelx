<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Return Details: #RET-') . $return->id }}
            </h2>
            <a href="{{ route('disputes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">&larr; Back to List</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Case Information -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium mb-4">Return Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Order ID</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">#{{ $return->order->id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Current Status</p>
                                <p class="font-medium">
                                     @php
                                        $color = match($return->status) {
                                            'pending' => 'text-yellow-600',
                                            'under_review' => 'text-blue-600',
                                            'resolved' => 'text-green-600',
                                            'rejected' => 'text-red-600',
                                            default => 'text-gray-600',
                                        };
                                    @endphp
                                    <span class="{{ $color }}">{{ ucfirst(str_replace('_', ' ', $return->status)) }}</span>
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-500">Reason</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $return->reason }}</p>
                            </div>
                        </div>

                        @if($return->evidences->isNotEmpty())
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Evidence Provided</h4>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($return->evidences as $evidence)
                                        <a href="{{ Storage::url($evidence->file_path) }}" target="_blank" class="block border rounded p-1 hover:border-indigo-500 transition">
                                            @if(Str::startsWith($evidence->file_type, 'image/'))
                                                <img src="{{ Storage::url($evidence->file_path) }}" class="h-20 w-full object-cover rounded" alt="Evidence">
                                            @else
                                                <div class="h-20 w-full flex items-center justify-center bg-gray-100 text-xs text-gray-500">File</div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Resolution (if resolved) -->
                    @if($return->status === 'resolved')
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-green-900 dark:text-green-400 mb-4">Case Resolution</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-green-100 dark:border-green-800/30 shadow-sm">
                                    <div>
                                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">Refund Type</p>
                                        <p class="text-xl font-black text-green-900 dark:text-green-300">
                                            {{ $return->resolution_type === 'full_refund' ? '✅ Full Refund' : '💰 Partial Refund' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">Approved Amount</p>
                                        <p class="text-2xl font-black text-green-900 dark:text-green-300">₹{{ number_format($return->refund_amount, 2) }}</p>
                                    </div>
                                </div>

                                <div class="text-sm p-4 bg-green-100/50 dark:bg-green-900/30 rounded-lg text-green-800 dark:text-green-400">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <div>
                                            <p class="font-bold mb-1">What happens next?</p>
                                            <p class="text-xs opacity-90 leading-relaxed">
                                                @if($return->resolution_type === 'full_refund')
                                                    Your entire payment of ₹{{ number_format($return->refund_amount, 2) }} has been approved for a refund.
                                                @else
                                                    A partial refund of ₹{{ number_format($return->refund_amount, 2) }} has been approved based on the resolution of your case.
                                                @endif
                                                Once claimed, the amount will be processed back to your original payment method within 5-7 business days.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm pt-2 border-t border-green-200 italic text-green-800 dark:text-green-400">
                                    &ldquo;Refund has been initiated and should reflect in your account within 5-7 business days.&rdquo;
                                </div>
                                
                                @if(!$return->razorpay_refund_id && $return->refund_amount > 0)
                                    <div class="mt-4 pt-4 border-t border-green-200 flex justify-center">
                                        <button 
                                            onclick="startRefundClaim({{ $return->order->id }}, {{ $return->refund_amount }})"
                                            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-black rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none shadow-lg transform hover:scale-105 transition-all"
                                        >
                                            💰 Claim Your ₹{{ number_format($return->refund_amount, 2) }} Refund Now
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($return->status === 'rejected')
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-red-900 dark:text-red-400 mb-2">Case Rejected</h3>
                            <p class="text-sm text-red-800 dark:text-red-300">
                                Your return request has been reviewed and rejected by our team. If you believe this is an error, please contact support.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Timeline / Logs -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Case History</h3>
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>
                        <div class="space-y-6">
                            @foreach($return->logs->sortByDesc('created_at') as $log)
                                <div class="relative pl-10">
                                    <div class="absolute left-2.5 top-1.5 w-3 h-3 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-800"></div>
                                    <div class="text-xs text-gray-500 mb-1">{{ $log->created_at->format('M d, H:i') }}</div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @if($log->new_status !== $log->old_status)
                                            Status change: <span class="font-bold border-b border-indigo-200">{{ ucfirst(str_replace('_', ' ', $log->new_status)) }}</span>
                                        @elseif(Str::contains($log->note, 'Seller response'))
                                            Seller provided a response
                                        @else
                                            Timeline Update
                                        @endif
                                    </div>
                                    @if($log->note)
                                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-900 border rounded text-xs text-gray-600 dark:text-gray-400 italic">
                                            {{ $log->note }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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

            // Show processing for 1s
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
