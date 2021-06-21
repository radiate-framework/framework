<?php

namespace Radiate\Auth;

use Radiate\Auth\Access\Gate;
use Radiate\Auth\JWT\Builder;
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
        $this->app->singleton('auth', function ($app) {
            return new AuthManager($app);
        });

        $this->app->singleton('jwt', function ($app) {
            return new Builder($app);
        });

        $this->app->singleton('gate', function ($app) {
            return new Gate($app, function () use ($app) {
                return call_user_func($app['auth']->userResolver());
            });
        });

        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                return call_user_func($app['auth']->userResolver());
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
        $this->commands([
            \Radiate\Auth\Console\MakePolicy::class,
        ]);
    }
}
