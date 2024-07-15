<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RazorpayPaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('/home', function () {
    return view('home');
})->middleware('auth');

Route::post('/recharge', [WalletController::class, 'recharge'])->name('recharge')->middleware('auth');

Route::post('/payment/process', [RazorpayPaymentController::class, 'processPayment'])->name('payment.process');
Route::post('/payment/callback', [RazorpayPaymentController::class, 'handleCallback'])->name('payment.callback');
Route::post('/wallet/pay', [WalletController::class, 'payFromWallet'])->name('wallet.pay')->middleware('auth');

