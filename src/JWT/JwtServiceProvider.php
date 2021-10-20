<?php

namespace Radiate\JWT;

use Firebase\JWT\JWT;
use Radiate\JWT\Manager;
use Radiate\Support\ServiceProvider;

class JwtServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager($app, new JWT);
        });
    }
}
