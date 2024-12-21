<?php

namespace App\Services\PaymentGateways;

use App\Interfaces\PaymentGateway;


class PayPalGateway implements PaymentGateway
{

    public function initiatePayment(array $data): string
    {
        return 'paypal redirection url';
    }

    public function verifyPayment(string $paymentId): bool
    {
        return true;
    }

    public function handleWebhook(array $payload): void
    {
    }
}
