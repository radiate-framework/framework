<?php

namespace Radiate\Schedule;

use Closure;
use Cron\CronExpression;
use Radiate\Foundation\Application;
use Radiate\Schedule\Concerns\ManagesFrequencies;

class Event
{
    use ManagesFrequencies;

    /**
     * The event callback
     *
     * @var callable|string
     */
    protected $callback;

    /**
     * Parameters to pass to the event callback
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * The event timezone
     *
     * @var \DateTimeZone|string|null
     */
    protected $timezone;

    /**
     * The CRON expression
     *
     * @var string
     */
    public $expression = '* * * * *';

    /**
     * The schedule description
     *
     * @var string
     */
    public $description;

    /**
     * The array of filter callbacks.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The array of reject callbacks.
     *
     * @var array
     */
    protected $rejects = [];

    /**
     * Create the event instance
     *
     * @param callable|string $callback
     * @param array $parameters
     * @param \DateTimeZone|string|null $timezone
     */
    public function __construct($callback, array $parameters = [], $timezone = null)
    {
        $this->callback = $callback;
        $this->parameters = $parameters;
        $this->timezone = $timezone;
    }

    /**
     * Get the CRON expression
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Get the event callback
     *
     * @return \Closure
     */
    public function event()
    {
        return function () {
            $container = Application::getInstance();

            return is_object($this->callback)
                ? $container->call([$this->callback, '__invoke'], $this->parameters)
                : $container->call($this->callback, $this->parameters);
        };
    }

    /**
     * Set the event name
     *
     * @param string $name
     * @return static
     */
    public function name(string $name)
    {
        $this->description = $name;

        return $this;
    }

    /**
     * Get the event description
     *
     * @return string
     */
    public function getSummaryForDisplay()
    {
        return $this->description;
    }

    /**
     * Get the next event run date
     *
     * @param string $currentTime
     * @param integer $nth
     * @param boolean $allowCurrentDate
     * @return \DateTime
     */
    public function nextRunDate(string $currentTime = 'now', int $nth = 0, bool $allowCurrentDate = false)
    {
        return (new CronExpression($this->getExpression()))
            ->getNextRunDate($currentTime, $nth, $allowCurrentDate, $this->timezone);
    }

    /**
     * Determine if the filters pass for the event.
     *
     * @param  \Radiate\Foundation\Application  $app
     * @return bool
     */
    public function filtersPass(Application $app): bool
    {
        foreach ($this->filters as $callback) {
            if (!$app->call($callback)) {
                return false;
            }
        }

        foreach ($this->rejects as $callback) {
            if ($app->call($callback)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Register a callback to further filter the schedule.
     *
     * @param  \Closure|bool  $callback
     * @return static
     */
    public function when($callback)
    {
        $this->filters[] = $callback instanceof Closure ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }

    /**
     * Register a callback to further filter the schedule.
     *
     * @param  \Closure|bool  $callback
     * @return static
     */
    public function skip($callback)
    {
        $this->rejects[] = $callback instanceof Closure ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }
}
