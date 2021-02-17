<?php

namespace Radiate\Foundation\Providers;

use Radiate\Support\ServiceProvider;

class CptServiceProvider extends ServiceProvider
{
    /**
     * The custom post types to register
     *
     * @var array
     */
    protected $postTypes = [];

    /**
     * Boot the app post types
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app['events']->listen('init', function () {
            foreach ($this->postTypes as $postType) {
                new $postType();
            }
        });
    }
}
