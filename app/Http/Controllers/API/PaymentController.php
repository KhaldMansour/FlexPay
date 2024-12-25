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

    /**
     * @OA\Post(
     *     path="/api/payment/initiate",
     *     summary="Initiate a payment process",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InitiatePaymentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment initiation successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Request successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="checkout_url",
     *                     type="string",
     *                     example="https://checkout.stripe.com/c/pay/cs_test_a1UjmkJYs4udW6d0TNPGZqZL2jf9GRt5sbSCRPK1pvvACblTQNfwvFXoeB#fidkdWxOYHwnPyd1blpxYHZxWjA0QnRUdzJNUk5nVTVMX31ITUxpTXMzX3IxUUcwUlV0dk5OTWd3cnU3RmBDTHNsfHRJPXw3PTx1Mn1VbTU9aGxgTHNPZnxocms2V0RRVH1WQ3BwdkwxPT1dNTVDdkpfV3YzcCcpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYHgl"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bad Request")
     *         )
     *     )
     * )
     */
    public function initiatePayment(InitiatePaymentRequest $request)
    {
        try {
            $paymentGateway = $request->input('gateway');

            return $this->successResponse(['checkout_url' => (new PaymentService($paymentGateway))->initiatePayment($request->all())]);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

     /**
     * @OA\Post(
     *     path="/api/payment/verify",
     *     summary="Verify a payment",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VerifyPaymentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment verification successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Request successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="successfulPayment",
     *                     type="boolean",
     *                     example=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bad Request")
     *         )
     *     )
     * )
     */
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
