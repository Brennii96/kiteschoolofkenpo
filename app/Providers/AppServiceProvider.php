<?php

namespace App\Providers;

use App\Listeners\ClearStatamicCache;
use App\Models\Member;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Statamic\Events\EntrySaved;
use Statamic\Facades\Cascade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(Member::class);
        Event::listen(EntrySaved::class, ClearStatamicCache::class);

        RedirectIfAuthenticated::redirectUsing(fn () => '/members/profile');

        Cascade::hydrated(function ($cascade) {
            $cascade->set('member', Auth::guard('member')->user());
        });

        // Statamic::vite('app', [
        //     'resources/js/cp.js',
        //     'resources/css/cp.css',
        // ]);
    }
}
