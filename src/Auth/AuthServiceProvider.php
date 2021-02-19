<?php

namespace Radiate\Auth;

use Radiate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('auth', function () {
            return new AuthManager();
        });

        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                return $app['auth']->user();
            });
        });
    }

    /**
     * Boot the services
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
