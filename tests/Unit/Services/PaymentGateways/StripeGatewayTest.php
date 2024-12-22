<?php

namespace Tests\Unit\Services\PaymentGateways;

use App\Services\PaymentGateways\StripeGateway;
use Mockery;
use Tests\TestCase;

class StripeGatewayTest extends TestCase
{
    protected $stripeGateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stripeGateway = new StripeGateway();
    }

    public function test_initiatePayment_success()
    {
        $stripeMock = Mockery::mock('alias:' . \Stripe\Checkout\Session::class);
        $stripeMock->shouldReceive('create')->andReturnSelf();
        $stripeMock->url = 'https://checkout.stripe.com/checkout_url';
        $data = [
            'gateway' => 'stripe',
            'product_name' => 'Test Product',
            'amount' => 100,
            'quantity' => 1,
            'currency' => 'USD',
        ];

        $result = $this->stripeGateway->initiatePayment($data);

        $this->assertEquals('https://checkout.stripe.com/checkout_url', $result);
    }

    public function test_initiatePayment_failure()
    {
        $stripeMock = Mockery::mock('alias:' . \Stripe\Checkout\Session::class);
        $stripeMock->shouldReceive('create')->andThrow(new \Exception('Stripe error'));
        $data = [
            'gateway' => 'stripe',
            'product_name' => 'Test Product',
            'amount' => 100,
            'quantity' => 1,
            'currency' => 'USD',
        ];

        $result = $this->stripeGateway->initiatePayment($data);

        $this->assertEquals('Stripe error', $result);
    }

    public function test_verifyPayment_success()
    {
        $mockPaymentIntent = Mockery::mock('alias:' . \Stripe\PaymentIntent::class);
        $mockPaymentIntent->shouldReceive('retrieve')
            ->with('payment_id')
            ->andReturnSelf();
        $mockPaymentIntent->status = 'succeeded'; // Simulate a successful payment
        $paymentId = 'payment_id';

        $result = $this->stripeGateway->verifyPayment($paymentId);

        $this->assertTrue($result);
    }

    public function test_verifyPayment_failure()
    {
        $mockPaymentIntent = Mockery::mock('alias:' . \Stripe\PaymentIntent::class);
        $mockPaymentIntent->shouldReceive('retrieve')
            ->with('payment_id')
            ->andReturnSelf();
        $mockPaymentIntent->status = 'Failed';
        $paymentId = 'payment_id';

        $result = $this->stripeGateway->verifyPayment($paymentId);

        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}