<?php

namespace Radiate\Schedule;

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
}
