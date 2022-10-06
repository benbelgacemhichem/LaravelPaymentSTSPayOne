<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentStsPayoneController;

Route::view('/', 'index');

# StsPayone routes 
Route::get('/sts/checkout', [PaymentStsPayoneController::class, 'generate_secure_hash']);
Route::get('/sts/pay', [PaymentStsPayoneController::class, 'redirect_checkout'])->name('redirect.checkout.stspayone');
Route::post('/sts/response', [PaymentStsPayoneController::class, 'response'])->name('response.stspayone');

Route::get('/sts/refund-action', [PaymentStsPayoneController::class, 'refund']);
Route::get('/sts/inquiry-action', [PaymentStsPayoneController::class, 'inquiryAction']);