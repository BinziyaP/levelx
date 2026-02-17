@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <svg class="mx-auto h-16 w-16 text-indigo-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Processing Payment...
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Please wait while we initialize the secure payment gateway.
            </p>
            
            <div class="mt-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow space-y-2">
                <h3 class="font-bold text-gray-900 dark:text-gray-100">Payment Summary</h3>
                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                    <span>Order Total:</span>
                    <span>₹{{ number_format($original_total ?? $total, 2) }}</span>
                </div>
                @if(isset($discount_amount) && $discount_amount > 0)
                <div class="flex justify-between text-green-600 font-medium">
                    <span>Bundle Discount ({{ $applied_rule['name'] ?? 'Applied' }}):</span>
                    <span>-₹{{ number_format($discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white border-t pt-2">
                    <span>Payable Amount:</span>
                    <span>₹{{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="mt-8">
            <p class="text-sm text-gray-500">
                If the payment window does not appear automatically, 
                <button id="rzp-button1" class="text-indigo-600 hover:text-indigo-500 font-medium focus:outline-none underline">
                    click here to try again
                </button>.
            </p>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $key }}",
        "amount": "{{ $amount }}",
        "currency": "INR",
        "name": "eShopy",
        "description": "Payment for Order #{{ $order_id }}",
        "image": "https://example.com/your_logo", // You might want to update this later
        "order_id": "{{ $order_id }}", 
        "handler": function (response){
            // Submit the form with payment details
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('payment.callback') }}";
            
            var hiddenField1 = document.createElement('input');
            hiddenField1.type = 'hidden';
            hiddenField1.name = '_token';
            hiddenField1.value = '{{ csrf_token() }}';
            form.appendChild(hiddenField1);

            var hiddenField2 = document.createElement('input');
            hiddenField2.type = 'hidden';
            hiddenField2.name = 'razorpay_payment_id';
            hiddenField2.value = response.razorpay_payment_id;
            form.appendChild(hiddenField2);

            var hiddenField3 = document.createElement('input');
            hiddenField3.type = 'hidden';
            hiddenField3.name = 'razorpay_order_id';
            hiddenField3.value = response.razorpay_order_id;
            form.appendChild(hiddenField3);

            var hiddenField4 = document.createElement('input');
            hiddenField4.type = 'hidden';
            hiddenField4.name = 'razorpay_signature';
            hiddenField4.value = response.razorpay_signature;
            form.appendChild(hiddenField4);

            document.body.appendChild(form);
            form.submit();
        },
        "prefill": {
            "name": "{{ auth()->user()->name }}",
            "email": "{{ auth()->user()->email }}",
            "contact": "" 
        },
        "theme": {
            "color": "#4F46E5" // Indigo-600 to match theme
        }
    };

    var rzp1 = new Razorpay(options);
    rzp1.on('payment.failed', function (response){
        alert("Payment Failed! Code: " + response.error.code + "\nDescription: " + response.error.description);
    });
    
    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
    
    // Automatically open payment modal
    window.onload = function() {
        // Short delay to ensure UI renders and looks intended before modal pops
        setTimeout(function() {
            rzp1.open();
        }, 500);
    };
</script>
@endsection
