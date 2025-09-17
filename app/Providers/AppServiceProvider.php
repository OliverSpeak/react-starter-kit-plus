<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        /*
         * Default password rules
         *
         * This is setup so that rules may be relaxed in non-production environments.
         */
        Password::defaults(function () {
            return $this->app->environment('production')
                ? Password::min(8) // ->letters()->numbers()
                : Password::min(0);
        });
    }
}
