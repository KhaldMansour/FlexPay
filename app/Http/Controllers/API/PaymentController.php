<?php

namespace App\Http\Controllers\API;

use App\Factories\PaymentGatewayFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\InitiatePaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function initiatePayment(InitiatePaymentRequest $request){
        $paymentGateway = PaymentGatewayFactory::make($request->input('gateway'));

        return $this->successResponse(['checkout_url' =>$paymentGateway->initiatePayment($request->all())]);
    }

    public function verifyPayment(Request $request){
        $paymentGateway = PaymentGatewayFactory::make($request->input('gateway'));

        return $this->successResponse(['successfulPayment' =>$paymentGateway->verifyPayment($request->input('paymentId'))]);
    }

    public function handleStripeWebhook(Request $request)
    {
        $paymentGateway = PaymentGatewayFactory::make('stripe');
        $paymentGateway->handleWebhook($request);
        
        return $this->successResponse(['status' => 'success']);
    }
}
