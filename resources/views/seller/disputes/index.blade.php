@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 dark:text-gray-200 text-3xl font-medium">Returns</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View return requests raised by buyers about your products.</p>

    @if(session('success'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200 dark:border-gray-700">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Return ID</th>
                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Buyer</th>
                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @forelse($disputes as $dispute)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $dispute->id }}</div>
                            <div class="text-xs text-gray-500">{{ $dispute->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-900 dark:text-gray-100">#{{ $dispute->order_id }}</div>
                            <div class="text-xs text-gray-500">₹{{ number_format($dispute->order->total_price, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $dispute->buyer->name }}</div>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">{{ $dispute->reason }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $dispute->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $dispute->status === 'under_review' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $dispute->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $dispute->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">
                            <a href="{{ route('seller.disputes.show', $dispute->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 border-b border-gray-200 dark:border-gray-700">
                            No return requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
