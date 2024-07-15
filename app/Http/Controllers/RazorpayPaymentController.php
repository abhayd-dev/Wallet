<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RazorpayPaymentController extends Controller
{
    protected $razorpayKey;
    protected $razorpaySecret;
    protected $api;

    public function __construct()
    {
        $this->razorpayKey = env('RAZORPAY_KEY');
        $this->razorpaySecret = env('RAZORPAY_SECRET');
        $this->api = new Api($this->razorpayKey, $this->razorpaySecret);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $request->amount * 100; // Convert to paise

        $orderData = [
            'receipt' => uniqid(),
            'amount' => $amount,
            'currency' => 'INR',
            'payment_capture' => 1
        ];

        $order = $this->api->order->create($orderData);

        return response()->json([
            'success' => true,
            'order_id' => $order['id'],
            'amount' => $order['amount']
        ]);
    }

    public function handleCallback(Request $request)
    {
        $data = $request->all();
    
        Log::info('Razorpay Callback Data: ', $data);
    
        $attributes = [
            'razorpay_order_id' => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
        ];
    
        $calculated_signature = hash_hmac('sha256', $attributes['razorpay_order_id'] . '|' . $attributes['razorpay_payment_id'], $this->razorpaySecret);
    
        if ($calculated_signature === $data['razorpay_signature']) {
            $amount = $data['amount'] ;  
            $user_id = Auth::id();
            DB::table('users')
                ->where('id', $user_id)
                ->increment('wallet_balance', $amount);
    
            return response()->json(['success' => true]);
        }
    
        return response()->json(['success' => false, 'error' => 'Signature verification failed']);
    }
    
}
