<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitiatePaymentRequest;
use App\Http\Requests\VerifyPaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiatePayment(InitiatePaymentRequest $request)
    {
        try {
            $paymentGateway = $request->input('gateway');

            return $this->successResponse(['checkout_url' => (new PaymentService($paymentGateway))->initiatePayment($request->all())]);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    public function verifyPayment(VerifyPaymentRequest $request)
    {
        try {
            return $this->successResponse(['successfulPayment' => (new PaymentService($request->input('gateway')))->verifyPayment($request->input('paymentId'))]);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    public function handleStripeWebhook(Request $request)
    {
        try {
            (new PaymentService('stripe'))->handleWebhook($request);

            return $this->successResponse(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->failureResponse($e->getMessage());
        }
    }
}
