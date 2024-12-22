# PayFlex
 An Online Payment Gateway Integration System

## Online Payment Gateway System

### A modular and scalable payment gateway integration system built with Laravel, adhering to SOLID principles, and containerized using Docker and Docker Compose. This system integrates **Stripe** as a proof of concept for online payments.

---

## Table of Contents

1. [Introduction](#introduction)
2. [Core Features](#core-features)
3. [Stripe Integration (Proof of Concept)](#stripe-integration-proof-of-concept)
4. [Architecture and Design](#architecture-and-design)
5. [Getting Started](#getting-started)
   - [Prerequisites](#prerequisites)
   - [Setup](#setup)
   - [Docker Setup](#docker-setup)
6. [Usage](#usage)
7. [Testing](#testing)
8. [Contributing](#contributing)
9. [License](#license)

---

## Introduction

The **Online Payment Gateway System** is designed to integrate multiple payment gateways (such as **Stripe**, **PayPal**, and others) into a Laravel-based application. The architecture is designed with flexibility in mind, enabling easy addition of new payment gateways without modifying existing code.

The system follows the **SOLID** principles for clean, maintainable, and scalable code, with modular components for payment initiation, verification, and handling webhooks. Additionally, it is containerized using **Docker** for easy setup and portability.

---

## Core Features

- **Dynamic Gateway Integration**: Add new payment gateways dynamically without changing existing codebase.
- **Payment Operations**:
  - Initiating a payment
  - Verifying payment status
  - Handling webhook notifications from external payment providers
- **SOLID Principles**: The system follows the SOLID principles
- **Extensible and Scalable**: Easily extendable to integrate additional payment providers.
- **Testable**: Unit tests have been implemented to ensure code reliability.

---

## Stripe Integration (Proof of Concept)

This system integrates **Stripe** as a proof of concept for online payments. The integration covers the following functionalities:

- **Initiating Payments**: The system allows initiating payments via **Stripe** by creating a checkout session using the `Stripe\Checkout\Session` class.
- **Payment Verification**: After a user completes the payment, the system verifies the payment status via **Stripe's PaymentIntent** API.
- **Webhooks**: The system listens for and processes **Stripe webhook notifications** to handle events such as payment success or failure.

This integration demonstrates the core functionality of integrating an online payment provider into the system and serves as a foundation for adding other gateways like **PayPal** or **Square**.

### Proof of Concept Details:

- **Payment Gateway**: Stripe
- **Key Operations**:
  - **Payment Initiation**: Users can initiate payments for products/services by providing details such as product name, price, quantity, and currency.
  - **Payment Verification**: After initiating the payment, the system verifies whether the payment is successful via the `verifyPayment` method.
  - **Webhook Handling**: Stripe webhook notifications are received and processed for payment updates (e.g., successful payments, failed payments).

---

## Architecture and Design

### Overview

The architecture of the payment gateway system is built around key modules:
1. **PaymentGatewayInterface**: An abstract contract that defines core operations for each gateway (e.g., `initiatePayment`, `verifyPayment`, and `handleWebhook`).
2. **PaymentGatewayFactory**: A factory class responsible for creating and returning the correct payment gateway instance based on the provided gateway name (e.g., Stripe, PayPal).
3. **PaymentGateway Implementations**: Specific classes for each payment gateway (e.g., `StripeGateway`, `PayPalGateway`), which implement the `PaymentGatewayInterface`.
4. **Controller**: A controller to handle requests from users (e.g., `PaymentController`) that interacts with the factory and payment gateway implementations.
5. **Webhooks**: A webhook handler to manage notifications from third-party gateways, ensuring payments are tracked and updated in the system.
6. **Docker**: The application is containerized using Docker and Docker Compose for seamless deployment and management.

### SOLID Principles Implementation

- **Single Responsibility Principle (SRP)**: Each class has a single responsibility. For example, `StripeGateway` handles only Stripe-specific logic.
- **Open/Closed Principle (OCP)**: New gateways can be added without modifying existing code by adding new classes that implement the `PaymentGatewayInterface`.
- **Liskov Substitution Principle (LSP)**: Any new payment gateway that implements the `PaymentGatewayInterface` can replace the existing one.
- **Interface Segregation Principle (ISP)**: The `PaymentGatewayInterface` is designed with minimal methods so that gateway classes are not burdened with unnecessary methods.
- **Dependency Inversion Principle (DIP)**: Classes depend on abstractions (interfaces) rather than concrete classes.

---

## Getting Started

### Prerequisites

- PHP >= 8.0
- Composer (for PHP dependency management)
- Docker & Docker Compose (for containerization)
- Laravel 8.x or above
- A .env file configured with necessary API keys for Stripe, PayPal, etc.

### Setup

1. **Clone the repository**:

   ```bash
   git clone https://github.com/your-username/payment-gateway-system.git
   cd payment-gateway-system
    ```
2. **Install PHP dependencies:**
    ```bash
        composer install
    ```

 3. **Set up the environment file:**
    Copy .env.example to .env and update your environment variables (e.g., database credentials, Stripe API keys):
    
    ```bash
    cp .env.example .env
    ```
    Example of .env configuration:

    ```
    STRIPE_SECRET_KEY=your_stripe_secret_key
    STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret_key
    APP_URL=http://localhost
    ```

 ### Docker Setup
 To set up the application using Docker, follow these steps:

 1. **Build Docker containers:**

    ```
    docker-compose build
    ```

 2. **Start Docker containers:**

    ```
    docker-compose up -d
    ```
 3. **Access the application:**

    The application will be accessible on http://localhost:8000.

4. **Run migrations:**

    Run the migrations:

    ```
    docker-compose exec app php artisan migrate
    ```

## Usage

### Initiating a Payment
You can initiate a payment using a POST request to the `/api/payment/initiate` endpoint. The request payload should include:
### Payment Request Parameters

The following are the required parameters for initiating a payment:

| Parameter    | Description                                                   |
|--------------|---------------------------------------------------------------|
| `gateway`    | The name of the payment gateway (e.g., `stripe`, `paypal`).   |
| `product_name` | The name of the product.                                      |
| `amount`     | The amount to be charged.                                     |
| `currency`   | The currency for the payment (e.g., `USD`).                   |
| `quantity`   | The quantity of the product.                                  |

**Request:**
```json
{
    "gateway": "stripe",
    "product_name": "Test Product",
    "amount": 100,
    "quantity": 1,
    "currency": "USD"
}
```

**Response:**
```json
{
    "status": 200,
    "data": {
        "checkout_url": "https://checkout.stripe.com/checkout_url"
    }
}
```

The response will return a checkout_url that the user can use to complete the payment on Stripe's checkout page.

### Verifying a Payment

To verify a payment, use the /api/payment/verify endpoint. You need to provide the payment ID in the request payload:
**Request::**
```json
{
    "gateway": "stripe",
    "paymentId": "pi_1FJvbnC2p6c67wEcZoLFzyqv"
}
```

**Response:**
```json
{
    "status": 200,
    "data": {
        "successfulPayment": true
    }
}
```


If the payment is successful, the response will return "successfulPayment": true. If the payment verification fails, it will return "successfulPayment": false.

## Testing
Unit tests for core functionality are located in the tests/ directory.

**Running Tests**
To run the tests, execute:

        ./vendor/bin/phpunit

You can also run tests within the Docker container by:

        docker-compose exec app ./vendor/bin/phpunit


## Docker Usage
To run the application using Docker and Docker Compose, follow these steps:

1. **Build and Start the Containers:**

Use the following command to build and start the containers:

```bash

docker-compose up --build
```
This will build the images and start the containers, including the database and the web server.

2. **Access the Application:**

Once the containers are up and running, access the application at http://localhost:8000.


## Contributing
We welcome contributions from the community. If you'd like to contribute, follow these steps:

1. Fork the repository.
2. Create a new branch (git checkout -b feature-name).
3. Make your changes and commit (git commit -am 'Add feature').
4. Push to the branch (git push origin feature-name).
5. Create a pull request describing your changes.

## License
This project is licensed under the MIT License.
