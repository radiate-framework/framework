<?php

namespace Radiate\JWT;

use Firebase\JWT\JWT as FirebaseJWT;
use Radiate\JWT\Manager;
use Radiate\Support\Facades\JWT;
use Radiate\Support\Facades\Request;
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
            return new Manager($app, new FirebaseJWT);
        });
    }

    /**
     * Boot the service provider
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRequestJwt();
    }

    /**
     * Register the "jwt" macro on the request.
     *
     * @return void
     */
    protected function registerRequestJwt()
    {
        Request::macro('jwt', function (?string $claim = null) {
            if ($claim) {
                return JWT::payload($this)->$claim;
            }

            return JWT::payload($this);
        });
    }
}
