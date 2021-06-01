<?php

namespace Radiate\Schedule;

use Radiate\Events\Dispatcher;
use Radiate\Foundation\Application;

class Schedule
{
    protected $app;
    protected $dispatcher;
    protected $events = [];

    public function __construct(Application $app, Dispatcher $dispatcher)
    {
        $this->app = $app;
        $this->dispatcher = $dispatcher;
    }
    /**
     * Add a new callback event to the schedule.
     *
     * @param  string|callable  $callback
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    public function call($callback, array $parameters = [])
    {
        $this->events[] = $event = new Event(
            $callback,
            $parameters,
            $this->timezone
        );

        return $event;
    }
    public function job($job)
    {
        return $this->call($job)->name(is_string($job) ? $job : get_class($job));
    }
    public function registerScheduledEvents()
    {
        foreach ($this->events as $event) {
            $this->registerScheduledEvent($event);
        }
    }
    public function registerScheduledEvent(Event $event)
    {
        $this->dispatcher->listen(
            $name = $event->getSummaryForDisplay(),
            $event->event()
        );

        if (!wp_next_scheduled($name)) {
            wp_schedule_single_event($event->nextRunDate()->format('U'), $name);
        }
    }
}
