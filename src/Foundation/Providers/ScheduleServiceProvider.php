<?php

namespace Radiate\Foundation\Providers;

use Radiate\Schedule\Schedule;
use Radiate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function boot()
    {
        $this->schedule($schedule = $this->app->make(Schedule::class));

        $schedule->registerScheduledEvents();
    }

    /**
     * Schedule events
     *
     * @param \Radiate\Schedule\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        //
    }
}
