<?php

namespace Radiate\Events;

use Radiate\Support\Facades\Event;

trait Dispatchable
{
    /**
     * Dispatch an event.
     *
     * @param mixed ...$args
     * @return void
     */
    public static function dispatch(...$args): void
    {
        Event::dispatch(new static(...$args));
    }
}
