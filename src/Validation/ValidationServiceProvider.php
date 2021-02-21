<?php

namespace Radiate\Validation;

use Radiate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('validator', function () {
            return new Factory();
        });
    }
}
