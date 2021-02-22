<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Radiate\View\View make(string $view, array $data = []) Make a view
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
