<?php

use App\Http\Controllers\API\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
Route::post('/payment/verify', [PaymentController::class, 'verifyPayment']);
Route::post('/stripe/webhook', [PaymentController::class, 'handleStripeWebhook']);

