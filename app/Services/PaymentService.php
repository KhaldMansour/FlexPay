<?php
namespace App\Services;

use App\Factories\PaymentGatewayFactory;
use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;

class PaymentService 
{
    private PaymentGatewayInterface $paymentGateway;

    public function __construct(string $paymentGateway)
    {
        $this->paymentGateway = PaymentGatewayFactory::make($paymentGateway);
    }

    public function initiatePayment(array $data)
    {
        return $this->paymentGateway->initiatePayment($data);
    }

    public function verifyPayment(string $paymentId)
    {
        return $this->paymentGateway->verifyPayment($paymentId);
    }

    public function handleWebhook(Request $request)
    {
        return $this->paymentGateway->handleWebhook($request);
    }
}
