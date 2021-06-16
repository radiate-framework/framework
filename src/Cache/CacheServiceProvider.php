<?php

namespace Radiate\Cache;

use Radiate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('cache', function ($app) {
            return new Repository(
                $app['db.connection'], $app['config']['cache.prefix'] ?? ''
            );
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
            \Radiate\Cache\Console\CacheClear::class,
            \Radiate\Cache\Console\CacheForget::class,
        ]);
        
        $this->publishes([
            __DIR__ . '/resources/config/cache.php' => $this->app->basePath('config/cache.php'),
        ], 'config');
    }
}
