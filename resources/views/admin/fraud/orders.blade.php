@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-red-700">⚠️ Suspicious Orders (Fraud Review)</h2>
        <a href="{{ route('admin.buyers') }}" class="text-indigo-600 hover:text-indigo-900">View All Orders</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-red-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-red-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Order ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Buyer Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Fraud Score</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->user->name ?? 'Unknown User' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ₹{{ number_format($order->total_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                Score: {{ $order->fraud_score }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Review</a>
                            
                            <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Only approve if you verify this order is legitimate. Continue?');">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 font-bold mr-2">Approve</button>
                            </form>

                            <form action="{{ route('admin.orders.decline', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to decline/cancel this fraud order?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Decline</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No suspicious orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
