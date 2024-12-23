<?php

namespace App\Providers;

use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PaymentService::class, function () {
            return new PaymentService(request()->input('gateway'));
        });
    }

    public function boot()
    {
    }
}
