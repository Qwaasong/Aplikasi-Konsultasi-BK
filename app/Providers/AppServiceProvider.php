<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(now()->addMinutes(1));
    }
}
