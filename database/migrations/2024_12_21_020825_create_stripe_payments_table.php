<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();  // Stripe Checkout session ID
            $table->string('payment_intent_id')->nullable();
            $table->string('payment_status');
            $table->decimal('amount_paid', 10, 2); 
            $table->string('currency', 3);
            $table->json('metadata')->nullable();
            $table->string('event_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_payments');
    }
};
