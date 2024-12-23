<?php

namespace App\Factories;

use App\Interfaces\PaymentGatewayInterface;
use App\Services\PaymentGateways\PayPalGateway;
use App\Services\PaymentGateways\StripeGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'stripe' => new StripeGateway(),
            'paypal' => new PayPalGateway(),
            default => throw new InvalidArgumentException("Unsupported gateway: $gateway"),
        };
    }
}