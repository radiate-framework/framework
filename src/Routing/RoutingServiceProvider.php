<?php

namespace Radiate\Routing;

use Radiate\Support\Facades\Request;
use Radiate\Support\Facades\URL;
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
            $url = new UrlGenerator($app['request'], $app['config']['app.asset_url']);

            $url->setKeyResolver(function () use ($app) {
                return $app['config']['app.key'];
            });

            return $url;
        });
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRequestSignatureValidation();

        $this->commands([
            \Radiate\Routing\Console\MakeController::class,
            \Radiate\Routing\Console\MakeMiddleware::class,
            \Radiate\Routing\Console\RouteList::class,
        ]);
    }

    /**
     * Register the "hasValidSignature" macro on the request.
     *
     * @return void
     */
    protected function registerRequestSignatureValidation()
    {
        Request::macro('hasValidSignature', function (): bool {
            /**
             * @var \Radiate\Http\Request $this
             */
            return URL::hasValidSignature($this);
        });
    }
}
