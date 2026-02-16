@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Complete Your Payment</h2>
            <p class="text-gray-600 dark:text-gray-300">Total Amount: ₹{{ $total }}</p>
        </div>
        <div class="px-6 py-4">
            <button id="rzp-button1" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Pay Now
            </button>
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
        "image": "https://example.com/your_logo",
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
            "color": "#3399cc"
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
        rzp1.open();
    };
</script>
@endsection
