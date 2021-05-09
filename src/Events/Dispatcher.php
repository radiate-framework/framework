<?php

namespace Radiate\Events;

class Dispatcher
{
    /**
     * Create an event listener
     *
     * @param string|string[] $events
     * @param mixed $listener
     * @param int $priority
     * @param int $argCount
     * @return void
     */
    public function listen($events, $listener, int $priority = 10, int $argCount = 5): void
    {
        $listener = $this->resolveListener($listener);

        foreach ((array) $events as $event) {
            add_filter($event, $listener, $priority, $argCount);
        }
    }

    /**
     * Determine if an event has listeners
     *
     * @param string $event
     * @return bool
     */
    public function hasListeners(string $event): bool
    {
        return (bool) has_filter($event);
    }

    /**
     * Undocumented function
     *
     * @param string $event
     * @param mixed ...$payload
     * @return mixed
     */
    public function dispatch($event, ...$payload)
    {
        if (is_object($event)) {
            return apply_filters(get_class($event), $event);
        }

        return apply_filters($event, ...$payload ?: [null]);
    }

    /**
     * Forget the event listeners
     *
     * @param string $event
     * @param string|null $listener
     * @param int|null $priority
     * @return void
     */
    public function forget(string $event, ?string $listener = null, ?int $priority = 10): void
    {
        if ($listener) {
            remove_filter($event, $listener, $priority);
        } else {
            remove_all_filters($event);
        }
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param string $subscriber
     * @return void
     */
    public function subscribe(string $subscriber): void
    {
        (new $subscriber)->subscribe($this);
    }

    /**
     * Resolve a listener
     *
     * @param mixed $listener
     * @return mixed
     */
    protected function resolveListener($listener)
    {
        if (is_string($listener) && class_exists($listener)) {
            return [new $listener, 'handle'];
        }

        return $listener;
    }
}
