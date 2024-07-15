<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function recharge(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'amount' => 'equired|numeric|min:1',
            ]);

            $user_id = Auth::id();

            DB::transaction(function () use ($user_id, $request) {
                $user = DB::table('users')->where('id', $user_id)->first();

                if (!$user) {
                    Log::error('User not found');
                    return response()->json([
                        'error' => 'Internal Server Error'
                    ], 500);
                }

                $new_balance = $user->wallet_balance + $request->input('amount');

                DB::table('users')->where('id', $user_id)->update(['wallet_balance' => $new_balance]);
            });

            $user = DB::table('users')->where('id', $user_id)->first();

            return response()->json([
                'wallet_balance' => $user->wallet_balance
            ]);
        } else {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }
    }


public function payFromWallet(Request $request)
{
    $amountToPay = $request->input('amount');

   
    $user = Auth::user();
    $currentBalance = $user->wallet_balance;

    if ($currentBalance >= $amountToPay) {
   
        DB::table('users')
            ->where('id', $user->id)
            ->decrement('wallet_balance', $amountToPay);

 
        $updatedUser = DB::table('users')->where('id', $user->id)->first();

        return response()->json([
            'success' => true,
            'wallet_balance' => $updatedUser->wallet_balance,
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => 'Insufficient balance in wallet.',
        ]);
    }
}

    public function testPay(Request $request)
    {
        $amountToDeduct = $request->input('amount');
        $user_id = Auth::id();

        DB::transaction(function () use ($user_id, $amountToDeduct) {
            $user = DB::table('users')->where('id', $user_id)->lockForUpdate()->first();

            if (!$user) {
                Log::error('User not found');
                return response()->json([
                    'error' => 'Internal Server Error'
                ], 500);
            }

            if ($amountToDeduct > 0 && $user->wallet_balance >= $amountToDeduct) {
                $new_balance = $user->wallet_balance - $amountToDeduct;
                DB::table('users')->where('id', $user_id)->update(['wallet_balance' => $new_balance]);
            } else {
                return response()->json([
                    'uccess' => false,
                    'error' => 'Insufficient funds in wallet.',
                ], 400);
            }
        });

        $user = DB::table('users')->where('id', $user_id)->first();

        return response()->json([
            'uccess' => true,
            'wallet_balance' => $user->wallet_balance,
        ]);
    }
}