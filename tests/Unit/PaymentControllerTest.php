<?php

namespace Tests\Unit;

use App\Factories\PaymentGatewayFactory;
use App\Http\Requests\InitiatePaymentRequest;
use App\Http\Controllers\API\PaymentController;
use App\Http\Requests\VerifyPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Testing\Fakes\Fake;
use Mockery;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    public function test_initiatePayment_successs()
    {
        $paymentGatewayMock = Mockery::mock();
        $paymentGatewayMock->shouldReceive('initiatePayment')
            ->once()
            ->with([
                'gateway' => 'stripe',
                'product_name' => 'Test Product',
                'amount' => 100,
                'quantity' => 1,
                'currency' => 'USD'
            ])
            ->andReturn('https://checkout.stripe.com/');
        $paymentGatewayFactoryMock = Mockery::mock('alias:' . PaymentGatewayFactory::class);
        $paymentGatewayFactoryMock->shouldReceive('make')
            ->with('stripe')
            ->andReturn($paymentGatewayMock);
        $requestMock = Mockery::mock(InitiatePaymentRequest::class);
        $requestMock->shouldReceive('input')->with('gateway')->andReturn('stripe');
        $requestMock->shouldReceive('all')->andReturn([
            'gateway' => 'stripe',
            'product_name' => 'Test Product',
            'amount' => 100,
            'quantity' => 1,
            'currency' => 'USD'
        ]);
        $controller = new PaymentController($paymentGatewayFactoryMock);

        $response = $controller->initiatePayment($requestMock);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals('https://checkout.stripe.com/', $response->getData()->data->checkout_url);
    }
    
    public function test_initiatePayment_failure()
    {
        $paymentGatewayMock = Mockery::mock();
        $paymentGatewayMock->shouldReceive('initiatePayment')
            ->once()
            ->andThrow(new \Exception('Payment gateway error'));
        $paymentGatewayFactoryMock = Mockery::mock('alias:' . PaymentGatewayFactory::class);
        $paymentGatewayFactoryMock->shouldReceive('make')
            ->with('stripe')
            ->andReturn($paymentGatewayMock);
        $requestMock = Mockery::mock(InitiatePaymentRequest::class);
        $requestMock->shouldReceive('input')->with('gateway')->andReturn('stripe');
        $requestMock->shouldReceive('all')->andReturn([
            'gateway' => 'stripe',
            'product_name' => 'Test Product',
            'amount' => 100,
            'quantity' => 1,
            'currency' => 'USD'
        ]);
        $controller = new PaymentController($paymentGatewayFactoryMock);

        $response = $controller->initiatePayment($requestMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertEquals('Payment gateway error', $response->getData()->message);
    }

    public function test_verifyPayment_success()
    {
        $paymentGatewayMock = Mockery::mock();
        $paymentGatewayMock->shouldReceive('verifyPayment')
            ->once()
            ->with('paymentId')
            ->andReturn(true);
        $paymentGatewayFactoryMock = Mockery::mock('alias:' . PaymentGatewayFactory::class);
        $paymentGatewayFactoryMock->shouldReceive('make')
            ->with('stripe')
            ->andReturn($paymentGatewayMock);
        $requestMock = Mockery::mock(VerifyPaymentRequest::class);
        $requestMock->shouldReceive('input')->with('gateway')->andReturn('stripe');
        $requestMock->shouldReceive('input')->with('paymentId')->andReturn('paymentId');
        $controller = new PaymentController($paymentGatewayFactoryMock);

        $response = $controller->verifyPayment($requestMock);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertTrue($response->getData()->data->successfulPayment);
    }

    public function test_verifyPayment_failure()
    {
        $paymentGatewayMock = Mockery::mock();
        $paymentGatewayMock->shouldReceive('verifyPayment')
            ->once()
            ->with('invalid_payment_id')
            ->andThrow(new \Exception('Verification failed'));
        $paymentGatewayFactoryMock = Mockery::mock('alias:' . PaymentGatewayFactory::class);
        $paymentGatewayFactoryMock->shouldReceive('make')
            ->with('stripe')
            ->andReturn($paymentGatewayMock);
        $requestMock = Mockery::mock(VerifyPaymentRequest::class);
        $requestMock->shouldReceive('input')->with('paymentId')->andReturn('invalid_payment_id');
        $requestMock->shouldReceive('input')->with('gateway')->andReturn('stripe');
        $controller = new PaymentController();

        $response = $controller->verifyPayment($requestMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertEquals('Verification failed', $response->getData()->message);
    }

    public function test_handleStripeWebhook_success()
    {
        $paymentGatewayMock = Mockery::mock();
        $paymentGatewayMock->shouldReceive('handleWebhook')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn(true);
        $paymentGatewayFactoryMock = Mockery::mock('alias:' . PaymentGatewayFactory::class);
        $paymentGatewayFactoryMock->shouldReceive('make')
            ->with('stripe')
            ->andReturn($paymentGatewayMock);
        $requestMock = Mockery::mock(Request::class);

        $controller = new PaymentController();

        $response = $controller->handleStripeWebhook($requestMock);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals('success', $response->getData()->data->status);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
