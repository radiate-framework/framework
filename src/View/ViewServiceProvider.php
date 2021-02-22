<?php

namespace Radiate\View;

use Radiate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('view', function ($app) {
            return new Factory($app['view.finder']);
        });

        $this->app->bind('view.finder', function ($app) {
            return new Finder($app['files'], $app['config']['view.path']);
        });
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/resources/views' => $this->app['config']['view.path'],
        ], 'views');

        $this->publishes([
            __DIR__ . '/resources/config/view.php' => $this->app->basePath('config/view.php'),
        ], 'config');
    }
}
