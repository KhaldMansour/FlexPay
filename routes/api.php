<?php

use App\Http\Controllers\API\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment')->group(function () {
    Route::post('/initiate', [PaymentController::class, 'initiatePayment']);
    Route::post('/verify', [PaymentController::class, 'verifyPayment']);
});
Route::post('/stripe/webhook', [PaymentController::class, 'handleStripeWebhook']);