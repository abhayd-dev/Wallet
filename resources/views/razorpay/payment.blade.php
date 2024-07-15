<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Payment</title>
    <!-- Include the Razorpay checkout script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <div class="container">
        <h2>Confirm Payment</h2>
        <p>Amount: ₹ {{ $amount / 100 }}</p>
        <form action="{{ route('payment.callback') }}" method="POST">
            @csrf
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <button type="submit" class="btn btn-primary">Confirm Payment</button>
        </form>
    </div>

    <script>
        var options = {
            "key": "{{ $order->id }}", 
            "amount": "{{ $amount }}", 
            "currency": "INR",
            "name": "Your Company Name",
            "description": "Payment for Wallet Recharge",
            "handler": function(response){
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('recharge-form').submit();
            },
            "prefill": {
                "name": "{{ Auth::user()->name }}",
                "email": "{{ Auth::user()->email }}"
            },
            "theme": {
                "color": "#F37254"
            }
        };

        var rzp = new Razorpay(options);

        $(document).ready(function() {
            $('.add-amount').click(function() {
                var amountToAdd = $(this).data('amount');
                var currentAmount = parseInt($('#amount').val()) || 0;
                var newAmount = currentAmount + amountToAdd;
                $('#amount').val(newAmount);
            });

            $('#recharge-button').click(function(event) {
                rzp.open();
                event.preventDefault();
            });
        });
    </script>
</body>
</html>
