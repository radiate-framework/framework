<?php

namespace Radiate\Validation;

use Radiate\Support\Facades\Request;
use Radiate\Support\Facades\Validator;
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
        $this->app->singleton('validator', function ($app) {
            return new Factory($app);
        });
    }

    /**
     * Add the validate method to the request
     *
     * @return void
     */
    public function boot()
    {
        Request::macro('validate', function (array $rules, $messages = []) {
            try {
                return Validator::make($this->all(), $rules, $messages)->validate();
            } catch (ValidationException $e) {
                return $e->errors();
            }
        });
    }
}
