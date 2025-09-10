<?php

namespace App\Providers;

use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(StripeClient::class, function ($app) {
            return new StripeClient([
                'api_key' => config('services.stripe.secret'),
                'stripe_version' => config('services.stripe.version'),
            ]);
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(StripeClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {}
}
