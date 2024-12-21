<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripePayment extends Model
{
    protected $table = 'stripe_payments';
    protected $fillable = ['session_id', 'payment_intent_id', 'payment_status', 'amount_paid', 'currency', 'metadata', 'event_type'];
}
