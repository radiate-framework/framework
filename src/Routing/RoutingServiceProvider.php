<?php

namespace Radiate\Routing;

use Radiate\Support\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });

        $this->app->singleton('url', function ($app) {
            return new UrlGenerator($app['request']);
        });
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            \Radiate\Routing\Console\MakeController::class,
            \Radiate\Routing\Console\MakeMiddleware::class,
        ]);
    }
}
