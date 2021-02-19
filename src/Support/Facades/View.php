<?php

namespace Radiate\Support\Facades;

/**
 * @method static string make(string $path, array $args = []) Make a view
 *
 * @see \Radiate\View\View
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
