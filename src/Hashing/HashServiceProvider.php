<?php

namespace Radiate\Hashing;

use Radiate\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider
{
    /**
     * Register the services
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('hash', function () {
            return new Hasher();
        });
    }
}
