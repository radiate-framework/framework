<?php

namespace Radiate\Foundation\Providers;

use Radiate\Foundation\Http\FormRequest;
use Radiate\Support\ServiceProvider;

class FormRequestServiceProvider extends ServiceProvider
{
    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->afterResolving(FormRequest::class, function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function ($request, $app) {
            $request = FormRequest::createFrom($app['request'], $request);

            $request->setContainer($app);
        });
    }
}
