<?php

namespace Radiate\Schedule;

use Cron\CronExpression;
use Radiate\Foundation\Application;
use Radiate\Schedule\Concerns\ManagesFrequencies;

class Event
{
    use ManagesFrequencies;
    protected $schedule;
    protected $callback;
    protected $parameters = [];
    protected $timezone;
    public $expression = '* * * * *';
    public $description;

    public function __construct($callback, $parameters = [], $timezone = null)
    {
        $this->callback = $callback;
        $this->parameters = $parameters;
        $this->timezone = $timezone;
    }
    public function getExpression()
    {
        return $this->expression;
    }
    public function event()
    {
        return function () {
            $container = Application::getInstance();

            $response = is_object($this->callback)
                ? $container->call([$this->callback, '__invoke'], $this->parameters)
                : $container->call($this->callback, $this->parameters);

            return $response;
        };
    }

    public function name(string $name)
    {
        $this->description = $name;

        return $this;
    }
    public function getSummaryForDisplay()
    {
        return $this->description;
    }
    public function nextRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
    {
        return (new CronExpression($this->getExpression()))
            ->getNextRunDate($currentTime, $nth, $allowCurrentDate, $this->timezone);
    }
}
