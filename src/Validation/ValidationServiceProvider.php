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
     * Boot the service provider
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRequestValidation();

        $this->commands([
            \Radiate\Validation\Console\MakeRule::class,
        ]);
    }

    /**
     * Register the "validate" macro on the request.
     *
     * @return void
     */
    protected function registerRequestValidation()
    {
        Request::macro('validate', function (array $rules, array $messages = []): array {
            /**
             * @var \Radiate\Http\Request $this
             */
            return Validator::make($this->all(), $rules, $messages)->validate();
        });
    }
}
