<?php

namespace App\Http\Controllers\API;

use App\Factories\PaymentGatewayFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\InitiatePaymentRequest;
use App\Http\Requests\VerifyPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiatePayment(InitiatePaymentRequest $request){
        try{
            $paymentGateway = PaymentGatewayFactory::make($request->input('gateway'));

            return $this->successResponse(['checkout_url' =>$paymentGateway->initiatePayment($request->all())]);

        }catch(\Exception $e){
            return $this->failureResponse($e->getMessage());
        }
    }

    public function verifyPayment(VerifyPaymentRequest $request){
        try{
            $paymentGateway = PaymentGatewayFactory::make($request->input('gateway'));

            return $this->successResponse(['successfulPayment' =>$paymentGateway->verifyPayment($request->input('paymentId'))]);
        }catch(\Exception $e){
            return $this->failureResponse($e->getMessage());
        }
    }

    public function handleStripeWebhook(Request $request)
    {
        try{
            $paymentGateway = PaymentGatewayFactory::make('stripe');
            $paymentGateway->handleWebhook($request);
            
            return $this->successResponse(['status' => 'success']);
        }catch(\Exception $e){
            Log::error($e->getMessage());   
        }
    }
}
