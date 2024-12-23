<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function initiatePayment(array $data): string;
    public function verifyPayment(string $paymentId);
    public function handleWebhook(Request $request): void;
}