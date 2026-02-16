@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Orders / Transactions Management</h2>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->user->name ?? 'Unknown User' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <ul class="list-disc list-inside">
                                @forelse($order->items as $id => $item)
                                    <li>
                                        {{ $item['name'] }}
                                    </li>
                                @empty
                                    <li>No items</li>
                                @endforelse
                            </ul>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{-- Logic to find seller from the first product in the order --}}
                            @php
                                $sellerName = 'N/A';
                                if (!empty($order->items)) {
                                    $firstItemId = array_key_first($order->items);
                                    // Assuming keys are product IDs as seen in CartController
                                    $product = \App\Models\Product::find($firstItemId);
                                    if ($product && $product->user) {
                                        $sellerName = $product->user->name;
                                    } elseif ($product) {
                                        $sellerName = 'Seller ID: ' . $product->user_id;
                                    } else {
                                        $sellerName = 'Product Deleted';
                                    }
                                }
                            @endphp
                            {{ $sellerName }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.orders.delete', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
