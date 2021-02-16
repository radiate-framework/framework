<?php

namespace Radiate\WordPress;

use Radiate\Support\ServiceProvider;

class WordPressServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot the services
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            \Radiate\WordPress\Console\MakeCpt::class,
            \Radiate\WordPress\Console\MakeTaxonomy::class,
        ]);
    }
}
