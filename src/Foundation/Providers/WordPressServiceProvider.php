<?php

namespace Radiate\Foundation\Providers;

use Radiate\Support\ServiceProvider;

class WordPressServiceProvider extends ServiceProvider
{
    /**
     * The post types to register
     *
     * @var array
     */
    protected $postTypes = [];

    /**
     * The shortcodes to register
     *
     * @var array
     */
    protected $shortcodes = [];

    /**
     * Boot the app post types
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app['events']->listen('init', function () {
            foreach ($this->postTypes as $postType) {
                $this->app->make($postType)->register();
            }

            foreach ($this->shortcodes as $shortcode) {
                $this->app->make($shortcode)->register();
            }
        });
    }
}
