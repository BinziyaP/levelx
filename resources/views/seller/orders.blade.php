@extends('layouts.seller')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium mb-4">Orders</h3>

    <div class="mt-8">
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Buyer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Shipment Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($notifications as $notification)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $notification->data['product_name'] ?? 'Unknown Product' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->data['buyer_name'] ?? 'Unknown Buyer' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    ₹{{ $notification->data['amount'] ?? '0' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @php
                                        $status = $notification->data['shipping_status'] ?? 'pending';
                                        $colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'packed' => 'bg-blue-100 text-blue-800',
                                            'shipped' => 'bg-purple-100 text-purple-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                        ];
                                        $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if(isset($notification->data['internal_order_id']))
                                        <div x-data="{ open: false, status: 'pending' }">
                                            <button @click="open = true" class="text-indigo-600 hover:text-indigo-900">Update Shipping</button>

                                            <!-- Modal -->
                                            <div x-show="open" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
                                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                                    </div>

                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                        <form action="{{ route('seller.orders.update-shipping', $notification->data['internal_order_id']) }}" method="POST">
                                                            @csrf
                                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                <div class="sm:flex sm:items-start">
                                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                                            Update Shipping Status
                                                                        </h3>
                                                                        <div class="mt-2">
                                                                            <div class="mb-4">
                                                                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                                                                <select name="shipping_status" x-model="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                                                    <option value="pending">Pending</option>
                                                                                    <option value="packed">Packed</option>
                                                                                    <option value="shipped">Shipped</option>
                                                                                    <option value="delivered">Delivered</option>
                                                                                </select>
                                                                            </div>

                                            <div x-show="['packed', 'shipped', 'delivered'].includes(status)" class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Tracking Number <span class="text-red-500">*</span></label>
                                                    <input type="text" name="tracking_number" value="{{ $notification->data['tracking_number'] ?? '' }}" :required="['packed', 'shipped', 'delivered'].includes(status)" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Courier Service <span class="text-red-500">*</span></label>
                                                    <select name="courier_code" :required="['packed', 'shipped', 'delivered'].includes(status)" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">Select Courier</option>
                                                        @foreach(config('couriers') as $code => $name)
                                                            <option value="{{ $code }}" {{ (isset($notification->data['courier_code']) && $notification->data['courier_code'] == $code) ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Save
                                </button>
                                <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Track Button --}}
        @if(in_array($notification->data['shipping_status'] ?? 'pending', ['packed', 'shipped', 'delivered']))
             <a href="{{ route('seller.orders.track', $notification->data['internal_order_id']) }}" class="ml-4 text-green-600 hover:text-green-900 font-medium">
                Track
            </a>
        @endif
                                    @else
                                        <span class="text-xs text-gray-400">Order ID N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection
