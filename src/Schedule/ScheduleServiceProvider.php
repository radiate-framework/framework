<?php

namespace Radiate\Schedule;

use Radiate\Schedule\Schedule;
use Radiate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Schedule::class, function ($app) {
            return new Schedule($app, $app['events']);
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
            \Radiate\Schedule\Console\ScheduleList::class,
            \Radiate\Schedule\Console\ScheduleRun::class,
        ]);
    }
}
