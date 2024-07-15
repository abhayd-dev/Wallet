@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="col-sm-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-inline balance">
                    <span class="font-weight-bolder"> <b>Your Wallet Balance :</b></span>
                    <span class="p-2 text-success text-right"><i class="fa fa-inr"></i> <b><span id="wallet-balance">{{ Auth::user()->wallet_balance }}</span></b></span>
                    <button type="button" id="test-pay-button" class="btn btn-danger ml-2"> Pay Now </button>
                </div>
            </div>
            <div class="card-body">
                <form id="recharge-form" class="mt-3 mb-3">
                    @csrf
                    <div class="form-group">
                        <label for="amount" class="mb-3"><b>Add Money to Wallet</b></label>
                        <input type="number" class="form-control border-danger-subtle" id="amount" name="amount" min="0" placeholder="Enter Amount">
                    </div>
                    <div class="btn-group mt-3" role="group" aria-label="Add Amount">
                        <button type="button" class="btn btn-light add-amount border-dark" data-amount="500">+500</button>
                        <button type="button" class="btn btn-light add-amount border-dark" data-amount="1000">+1000</button>
                        <button type="button" class="btn btn-light add-amount border-dark" data-amount="1500">+1500</button>
                        <button type="button" class="btn btn-light add-amount border-dark" data-amount="2000">+2000</button>
                    </div>
                    <div class="form-group mt-3 text-center">
                        <button type="button" id="recharge-button" class="btn btn-warning col-sm-12"><b>Add Money To Wallet</b></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="insufficientBalanceModal" tabindex="-1" aria-labelledby="insufficientBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="insufficientBalanceModalLabel">Insufficient Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Your wallet balance is insufficient. Please add money to your wallet.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="modal-add-money-btn" data-dismiss="modal">Add Money</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {
    // Add money modal event
    $('#insufficientBalanceModal').on('shown.bs.modal', function () {
        $('#modal-add-money-btn').focus(); // Focus on the "Add Money" button when modal is shown
    });

    // Auto focus on amount input when modal is closed and reopened
    $('#insufficientBalanceModal').on('hidden.bs.modal', function () {
        $('#amount').focus(); // Focus on the amount input box when modal is closed
    });

    // Add amount buttons click event
    $('.add-amount').click(function() {
        var amountToAdd = parseInt($(this).data('amount'));
        var currentAmount = parseInt($('#amount').val()) || 0;
        var newAmount = currentAmount + amountToAdd;
        $('#amount').val(newAmount);
    });

    // Recharge button click event
    $('#recharge-button').click(function() {
        var amountToRecharge = $('#amount').val();
        if (amountToRecharge >= 100) { // Check if amount is at least 100
            // Proceed with payment logic
            $.ajax({
                url: '{{ route('payment.process') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: amountToRecharge
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var options = {
                            key: '{{ env('RAZORPAY_KEY') }}',
                            amount: response.amount,
                            currency: 'INR',
                            name: 'Papaya Coders',
                            description: 'Wallet Recharge',
                            image: 'https://papayacoders.in/wp-content/uploads/2023/06/papayacoders-logo-e1686920327207.png.webp',
                            order_id: response.order_id,
                            handler: function(paymentResponse) {
                                $.ajax({
                                    url: '{{ route('payment.callback') }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        razorpay_order_id: paymentResponse.razorpay_order_id,
                                        razorpay_payment_id: paymentResponse.razorpay_payment_id,
                                        razorpay_signature: paymentResponse.razorpay_signature,
                                        amount: amountToRecharge
                                    },
                                    success: function(result) {
                                        if (result.success) {
                                            Swal.fire({
                                                title: 'Payment Successful!',
                                                text: 'Your payment was successful.',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Payment Failed!',
                                                text: 'Failed to verify payment. Please contact support.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire({
                                            title: 'Payment Failed!',
                                            text: 'There was an error processing your payment. Please try again.',
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                            },
                            prefill: {
                                name: '{{ Auth::user()->name }}',
                                email: '{{ Auth::user()->email }}',
                                contact: '{{ Auth::user()->phone }}'
                            },
                            notes: {
                                address: 'Customer Address'
                            },
                            theme: {
                                color: '#3399cc'
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else {
                        alert('Failed to initiate Razorpay payment.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Failed to initiate Razorpay payment. Please try again.');
                }
            });
        } else {
            Swal.fire({
                title: 'Minimum Amount Requirement',
                text: 'Please enter a minimum amount of Rs. 100 to recharge your wallet.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    });

    // Test pay button click event
    $('#test-pay-button').click(function() {
        var amountToPay = $('#amount').val();
        if (amountToPay > 0) {
            // Proceed with payment logic
            $.ajax({
                url: '{{ route('wallet.pay') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: amountToPay
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Payment Successful!',
                            text: 'Amount deducted from your wallet.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#wallet-balance').text(response.wallet_balance);
                        });
                    } else {
                        Swal.fire({
                            title: 'Payment Failed!',
                            text: response.error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Payment Failed!',
                        text: 'There was an error processing your payment. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        } else {
            alert('Please enter a valid amount to pay.');
        }
    });

    $('#modal-ok-btn').click(function() {
        // Hide the modal
        $('#insufficientBalanceModal').modal('hide');
    });

    // Check wallet balance on page load
    var currentBalance = parseFloat($('#wallet-balance').text());
    if (currentBalance <= 0) {
        $('#insufficientBalanceModal').modal('show');
    }
});


</script>
@endsection
