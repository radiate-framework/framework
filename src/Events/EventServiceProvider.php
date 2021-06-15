<?php

namespace Radiate\Events;

use Radiate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the services
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('events', function ($app) {
            return new Dispatcher($app);
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
            \Radiate\Events\Console\MakeEvent::class,
            \Radiate\Events\Console\MakeListener::class,
            \Radiate\Events\Console\MakeSubscriber::class,
        ]);
    }
}
