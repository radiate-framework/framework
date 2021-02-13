<?php

namespace Radiate\Support\Facades;

/**
 * @method static void listen(string|string[] $events, mixed $listener, int $priority = 10, int $argCount = 5) Create an event listener
 * @method static bool hasListeners(string $event) Determine if an event has listeners
 * @method static mixed dispatch(string $event, mixed ...$payload) Dispatch an event
 * @method static void forget(string $event) Forget the event listeners
 * @method static void subscribe(string $subscriber) Register an event subscriber with the dispatcher.
 * @method static mixed resolveListener(mixed $listener) Resolve a listener
 *
 * @see \Radiate\Events\Dispatcher
 */
class Event extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'events';
    }
}
