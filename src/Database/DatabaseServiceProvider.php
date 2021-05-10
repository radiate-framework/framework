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
        $this->app->singleton('db.connection', function () {
            global $wpdb;
            return $wpdb;
        });
        $this->app->singleton('db', function () {
            // return the builder instance
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
