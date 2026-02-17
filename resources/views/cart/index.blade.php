@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Shopping Cart') }}
    </h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        @if(session('cart'))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @php $total = 0 @endphp
                        @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $details['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    ₹{{ $details['price'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $details['quantity'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    ₹{{ $details['price'] * $details['quantity'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end space-y-1">
                                    @if(isset($calculation))
                                        @if($calculation['discount_amount'] > 0)
                                            <div class="text-gray-500 text-sm">
                                                Original: <span class="line-through">₹{{ number_format($calculation['original_total'], 2) }}</span>
                                            </div>
                                            <div class="text-green-600 text-sm font-semibold">
                                                Discount: -₹<span id="discount-amount">{{ number_format($calculation['discount_amount'], 2) }}</span>
                                                @if(!empty($calculation['applied_rule']))
                                                    <div class="text-xs text-green-700">
                                                        {{ $calculation['applied_rule']['name'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            Total: ₹<span id="final-total">{{ number_format($calculation['final_total'], 2) }}</span>
                                        </h3>
                                        <!-- breakdown message if any -->
                                        @if(isset($calculation['breakdown']['message']))
                                            <p class="text-xs text-gray-500">{{ $calculation['breakdown']['message'] }}</p>
                                        @endif
                                    @else
                                        <!-- Fallback for simple total if calculation missing -->
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Total: ₹{{ number_format($total, 2) }}</h3>
                                    @endif
                                    <div id="api-status" class="text-xs text-gray-400 italic"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right">
                                <a href="{{ route('shop') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors mr-2">
                                    Continue Shopping
                                </a>
                                <form action="{{ route('checkout.process') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                        Buy Now
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-gray-500 text-lg mb-4">Your cart is empty.</p>
                <a href="{{ route('shop') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Go to Shop
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cart = @json(session('cart', []));
        if (Object.keys(cart).length === 0) return;

        // Transform cart object to array for API if needed, or send as is if Controller handles it.
        // Controller handles key-value pairs or list.
        // Let's send as object.
        
        fetch('/api/bundle/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items: cart })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Bundle API Result:', data.data);
                const result = data.data;
                // We can strictly update the DOM here to prove frontend is source of update?
                // But server already rendered it. We'll verify it matches.
                
                // document.getElementById('final-total').innerText = result.final_total;
                // document.getElementById('api-status').innerText = 'Verified via API';
            }
        })
        .catch(error => console.error('Error fetching bundle pricing:', error));
    });
</script>
@endsection
