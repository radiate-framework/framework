<?php

namespace Radiate\Foundation\Providers;

use Radiate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Map the routes
     *
     * @return void
     */
    public function map()
    {
        //
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot()
    {
        $this->map();
    }
}
