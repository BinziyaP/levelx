@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-gray-700 dark:text-gray-200 text-3xl font-medium">Return Case #{{ $dispute->id }}</h3>
            <p class="text-sm text-gray-500 mt-1">Order #{{ $dispute->order_id }} &middot; Filed {{ $dispute->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('admin.disputes.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">← Back to Returns</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content (Left 2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Order Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Order Details</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Order ID:</span> <span class="text-gray-900 dark:text-gray-100">#{{ $dispute->order->id }}</span></div>
                    <div><span class="text-gray-500">Total:</span> <span class="text-gray-900 dark:text-gray-100 font-semibold">₹{{ number_format($dispute->order->total_price, 2) }}</span></div>
                    <div><span class="text-gray-500">Status:</span> <span class="text-gray-900 dark:text-gray-100">{{ ucfirst($dispute->order->status) }}</span></div>
                    <div><span class="text-gray-500">Shipping:</span> <span class="text-gray-900 dark:text-gray-100">{{ ucfirst($dispute->order->shipping_status ?? 'N/A') }}</span></div>
                    <div><span class="text-gray-500">Payment ID:</span> <span class="text-gray-900 dark:text-gray-100 text-xs">{{ $dispute->order->payment_id ?? 'N/A' }}</span></div>
                    <div><span class="text-gray-500">Placed:</span> <span class="text-gray-900 dark:text-gray-100">{{ $dispute->order->created_at->format('d M Y') }}</span></div>
                </div>

                {{-- Order Items --}}
                @php
                    $items = is_string($dispute->order->items) ? json_decode($dispute->order->items, true) : $dispute->order->items;
                @endphp
                @if(is_array($items))
                    <div class="mt-4 border-t pt-3">
                        <h5 class="text-sm font-medium text-gray-500 mb-2">Items</h5>
                        @foreach($items as $key => $item)
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $item['name'] ?? 'Product' }} × {{ $item['quantity'] ?? 1 }} — ₹{{ number_format($item['price'] ?? 0, 2) }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Buyer Complaint --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Buyer Complaint</h4>
                <div class="space-y-2">
                    <p class="text-sm"><span class="font-medium text-gray-500">Buyer:</span> <span class="text-gray-900 dark:text-gray-100">{{ $dispute->buyer?->name ?? 'Unknown' }} ({{ $dispute->buyer?->email ?? 'N/A' }})</span></p>
                    <p class="text-sm"><span class="font-medium text-gray-500">Reason:</span> <span class="text-gray-900 dark:text-gray-100">{{ $return->reason ?? $dispute->reason }}</span></p>
                </div>
            </div>

            {{-- Evidence Files --}}
            @if($dispute->evidences->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Evidence Files ({{ $dispute->evidences->count() }})</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($dispute->evidences as $evidence)
                        @if($evidence->file_type === 'image')
                            <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank" class="block rounded-lg border overflow-hidden hover:shadow-lg transition">
                                <img src="{{ asset('storage/' . $evidence->file_path) }}" alt="Evidence" class="w-full h-32 object-cover">
                                <div class="px-2 py-1 text-xs text-gray-500 bg-gray-50 dark:bg-gray-700">Image</div>
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank" class="flex items-center justify-center h-32 bg-gray-50 dark:bg-gray-700 rounded-lg border hover:shadow-lg transition">
                                <div class="text-center">
                                    <span class="text-3xl">📄</span>
                                    <p class="text-xs text-gray-500 mt-1">PDF Document</p>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Seller Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Seller</h4>
                <p class="text-sm"><span class="text-gray-500">Name:</span> <span class="text-gray-900 dark:text-gray-100">{{ $dispute->seller?->name ?? 'Unknown' }}</span></p>
                <p class="text-sm"><span class="text-gray-500">Email:</span> <span class="text-gray-900 dark:text-gray-100">{{ $dispute->seller?->email ?? 'N/A' }}</span></p>
            </div>

            {{-- Fraud Score (Optional Integration) --}}
            @if($dispute->order->is_suspicious)
            <div class="bg-red-50 dark:bg-red-900 rounded-lg shadow p-6 border border-red-200">
                <h4 class="font-bold text-lg mb-2 text-red-800">⚠️ Fraud Alert</h4>
                <p class="text-sm text-red-700">This order was flagged as suspicious with a fraud score of <strong>{{ $dispute->order->fraud_score }}</strong>.</p>
            </div>
            @endif
        </div>

        {{-- Sidebar (Right 1/3) --}}
        <div class="space-y-6">
            {{-- Status & Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-sm mb-3 text-gray-500 uppercase tracking-wider">Case Status</h4>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    {{ $dispute->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $dispute->status === 'under_review' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $dispute->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $dispute->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}
                </span>

                @if($dispute->resolution_type)
                    <div class="mt-3 space-y-1 text-sm">
                        <p><span class="text-gray-500">Resolution:</span> <span class="text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $dispute->resolution_type)) }}</span></p>
                        @if($dispute->refund_amount > 0)
                            <p class="text-green-600 font-bold text-lg">Refund: ₹{{ number_format($dispute->refund_amount, 2) }}</p>
                        @endif
                        @if($dispute->resolvedBy)
                            <p class="text-xs text-gray-500">Resolved by {{ $dispute->resolvedBy->name }} on {{ $dispute->resolved_at->format('d M Y') }}</p>
                        @endif
                    </div>
                @endif

                {{-- Admin Action Buttons --}}
                <div class="mt-6 space-y-3">
                    {{-- Pending → Under Review --}}
                    @if($dispute->status === 'pending')
                        <form action="{{ route('admin.disputes.update-status', $dispute->id) }}" method="POST" onsubmit="return confirm('Mark this dispute as Under Review?');">
                            @csrf
                            <input type="hidden" name="status" value="under_review">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium transition">
                                📋 Mark as Under Review
                            </button>
                        </form>
                    @endif

                    {{-- Under Review → Resolve/Reject --}}
                    @if($dispute->status === 'under_review')
                        <button type="button" onclick="document.getElementById('resolve-modal').classList.remove('hidden')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition">
                            ✅ Resolve Return Request
                        </button>

                        <form action="{{ route('admin.disputes.update-status', $dispute->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this dispute?');">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <input type="hidden" name="note" value="Return request rejected by admin.">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition">
                                ❌ Reject Return Request
                            </button>
                        </form>
                    @endif

                    {{-- Terminal states --}}
                    @if(in_array($dispute->status, ['resolved', 'rejected', 'approved']))
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded text-center text-sm text-gray-500 font-medium border border-dashed">
                            🔒 This case is closed. No further actions available.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-sm mb-3 text-gray-500 uppercase tracking-wider">Timeline</h4>
                <div class="space-y-4">
                    @foreach($dispute->logs as $log)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full
                                {{ $log->new_status === 'pending' ? 'bg-yellow-400' : '' }}
                                {{ $log->new_status === 'under_review' ? 'bg-blue-400' : '' }}
                                {{ $log->new_status === 'resolved' ? 'bg-green-400' : '' }}
                                {{ $log->new_status === 'rejected' ? 'bg-red-400' : '' }}">
                            </div>
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        @if($log->new_status !== $log->old_status)
                                            Status: {{ ucfirst(str_replace('_', ' ', $log->new_status)) }}
                                        @elseif(Str::contains($log->note, 'Seller response'))
                                            Seller Response
                                        @else
                                            Internal Update
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log->note }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $log->created_at->format('d M Y, h:i A') }}
                                        @if($log->changer) — {{ $log->changer->name }} @endif
                                    </p>
                                </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Resolve Modal --}}
@if($dispute->status === 'under_review')
<div id="resolve-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="resolve-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('resolve-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.disputes.resolve', $dispute->id) }}" method="POST" id="resolve-form">
                @csrf
                <input type="hidden" name="action" value="resolve">

                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4" id="resolve-modal-title">Resolve Return Request #{{ $dispute->id }}</h3>

                    {{-- Refund Type Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Refund Type</label>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="resolution_type" value="full_refund" onchange="document.getElementById('partial-amount').classList.add('hidden')" class="text-indigo-600">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Full Refund</span>
                                    <p class="text-xs text-gray-500">₹{{ number_format($dispute->order->total_price, 2) }}</p>
                                </div>
                            </label>
                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="resolution_type" value="partial_refund" onchange="document.getElementById('partial-amount').classList.remove('hidden')" class="text-indigo-600">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Partial Refund</span>
                                    <p class="text-xs text-gray-500">Specify amount (max ₹{{ number_format($dispute->order->total_price, 2) }})</p>
                                </div>
                            </label>

                        </div>
                    </div>

                    {{-- Partial Refund Amount Input --}}
                    <div id="partial-amount" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Refund Amount (₹)</label>
                        <input type="number" name="refund_amount" step="0.01" min="0.01" max="{{ $dispute->order->total_price }}" placeholder="Enter refund amount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>

                    {{-- Admin Note --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note (optional)</label>
                        <textarea name="note" rows="3" maxlength="1000" placeholder="Add a note about the resolution..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Resolution
                    </button>
                    <button type="button" onclick="document.getElementById('resolve-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
