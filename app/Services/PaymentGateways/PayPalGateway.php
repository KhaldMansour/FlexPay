<?php

namespace App\Services\PaymentGateways;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;

class PayPalGateway implements PaymentGatewayInterface
{

    public function initiatePayment(array $data): string
    {
        return 'paypal redirection url';
    }

    public function verifyPayment(string $paymentId): bool
    {
        return true;
    }

    public function handleWebhook(Request $request): void
    {
    }
}
