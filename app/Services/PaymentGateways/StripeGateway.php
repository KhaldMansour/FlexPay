<?php

namespace App\Services\PaymentGateways;

use App\Interfaces\PaymentGateway;
use App\Models\StripePayment;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

use function Illuminate\Log\log;

class StripeGateway implements PaymentGateway
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function initiatePayment(array $data): string
    {
        try {
        $checkoutItem = $this->prepareCheckoutItem($data);
        $session = $this->startSession($checkoutItem);

        return $session->url;
        
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function verifyPayment(string $paymentId): bool
    {
        try {
            $session = PaymentIntent::retrieve($paymentId);
            if ($session->status === 'succeeded') {
                return true;
            } else {
                return false;
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return false;
        }
    }

    public function handleWebhook($request): void
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpointSecret);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->savePaymentSessionData($event , 'success');
                    break;
                case 'payment_intent.payment_failed':
                    $this->savePaymentSessionData($event , 'failure');
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            throw $e;
        };
    }

    private function prepareCheckoutItem(array $data): array
    {
        return [
            'price_data' => [
                'currency' => $data['currency'],
                'product_data' => [
                    'name' => $data['product_name'],
                ],
                'unit_amount' => $data['amount'] * 100,
            ],
            'quantity' => $data['quantity'],
        ];
    }

    private function startSession(array $checkoutItem) : Session{
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$checkoutItem],
            'mode' => 'payment',
            'success_url' => env('APP_URL'),
            'cancel_url' => env('APP_URL'),
            'metadata' => [
                'product_name' => $checkoutItem['price_data']['product_data']['name'],  
                'price' => $checkoutItem['price_data']['unit_amount'] / 100,
                'quantity' => $checkoutItem['quantity'],
            ],
        ]);
    }

    private function savePaymentSessionData($event , $status): void{
        $session = $event->data->object;
        $stripePayment = new StripePayment();
        $stripePayment->session_id = $session->id;
        $stripePayment->payment_intent_id = $session->id;
        $stripePayment->payment_status = $status;
        $stripePayment->amount_paid = $session->charges->data[0]->amount / 100;
        $stripePayment->currency = $session->currency;
        $stripePayment->metadata = json_encode($session->metadata);
        $stripePayment->event_type = $event->type;
        $stripePayment->save();
    }
}
