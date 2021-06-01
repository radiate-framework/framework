<?php

namespace Radiate\Schedule;

use Radiate\Events\Dispatcher;
use Radiate\Foundation\Application;

class Schedule
{
    /**
     * The application instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * The event dispatcher
     *
     * @var \Radiate\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * The registered events
     *
     * @var array
     */
    protected $events = [];

    /**
     * Create the scheduler instance
     *
     * @param \Radiate\Foundation\Application $app
     * @param \Radiate\Events\Dispatcher $dispatcher
     */
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
     * @return \Radiate\Schedule\Event
     */
    public function call($callback, array $parameters = [])
    {
        return $this->events[] = new Event($callback, $parameters);
    }

    /**
     * Schedule a new job
     *
     * @param object|string $job
     * @return \Radiate\Schedule\Event
     */
    public function job($job)
    {
        return $this->call($job)->name(is_string($job) ? $job : get_class($job));
    }

    /**
     * Register the scheduled events
     *
     * @return void
     */
    public function registerScheduledEvents()
    {
        foreach ($this->events as $event) {
            $this->registerScheduledEvent($event);
        }
    }

    /**
     * Register a scheduled event
     *
     * @param \Radiate\Schedule\Event $event
     * @return void
     */
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
