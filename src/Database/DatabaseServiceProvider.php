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
            return new Connection($wpdb);
        });

        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app['db.connection']);
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
