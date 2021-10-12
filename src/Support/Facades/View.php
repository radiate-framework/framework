<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Radiate\View\View file(string $view, array $data = []) Get the evaluated view contents for the given view.
 * @method static \Radiate\View\View make(string $view, array $data = []) Get the evaluated view contents for the given view.
 * @method static \Radiate\Support\Markdown markdown(string $view, array $data = []) Get the markdown contents for the given view.
 * @method static mixed share(array|string $key, mixed|null $value = null) Add a piece of shared data to the environment.
 * @method static mixed shared(string $key, mixed $default = null) Get an item from the shared data.
 * @method static array getShared(): array Get all of the shared data for the environment.
 * @method static bool exists(string $view) Determine if a given view exists.
 *
 * @see \Radiate\View\Factory
 */
class View extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'view';
    }
}
