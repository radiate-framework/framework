<?php

namespace Radiate\Database;

use Radiate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('db', function () {
            global $wpdb;

            return $wpdb;
        });
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot(): void
    {
        $this->commands([
            \Radiate\Database\Console\MakeModel::class,
        ]);
    }
}
