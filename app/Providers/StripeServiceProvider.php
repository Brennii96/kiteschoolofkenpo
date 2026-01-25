<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\StripeServiceInterface;
use App\Services\StripeService;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function () {
            return new StripeClient(config('cashier.secret'));
        });

        $this->app->bind(StripeServiceInterface::class, StripeService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
