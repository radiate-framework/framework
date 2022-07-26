<?php

namespace Radiate\View;

use Radiate\Support\ServiceProvider;
use Radiate\View\Compilers\BladeCompiler;
use Radiate\View\Engines\CompilerEngine;
use Radiate\View\Engines\FileEngine;
use Radiate\View\Engines\PhpEngine;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('view', function ($app) {
            return new Factory($app['view.engine.resolver'], $app['view.finder']);
        });

        $this->app->bind('view.finder', function ($app) {
            return new Finder($app['files'], $app['config']['view.path']);
        });

        $this->app->singleton('blade.compiler', function ($app) {
            return new BladeCompiler($app['files'], $app['config']['view']['compiled']);
        });

        $this->app->singleton('view.engine.resolver', function () {
            $resolver = new EngineResolver();

            $this->registerFileEngine($resolver);
            $this->registerPhpEngine($resolver);
            $this->registerBladeEngine($resolver);

            return $resolver;
        });
    }

    /**
     * Register the file engine
     *
     * @param \Radiate\View\EngineResolver $resolver
     * @return void
     */
    protected function registerFileEngine(EngineResolver $resolver): void
    {
        $resolver->register('file', function () {
            return new FileEngine($this->app['files']);
        });
    }

    /**
     * Register the php engine
     *
     * @param \Radiate\View\EngineResolver $resolver
     * @return void
     */
    protected function registerPhpEngine(EngineResolver $resolver): void
    {
        $resolver->register('php', function () {
            return new PhpEngine($this->app['files']);
        });
    }

    /**
     * Register the blade engine
     *
     * @param \Radiate\View\EngineResolver $resolver
     * @return void
     */
    protected function registerBladeEngine(EngineResolver $resolver): void
    {
        $resolver->register('blade', function () {
            return new CompilerEngine($this->app['blade.compiler'], $this->app['files']);
        });
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/resources/views' => $this->app['config']['view.path'],
        ], 'views');

        $this->publishes([
            __DIR__ . '/resources/config/view.php' => $this->app->basePath('config/view.php'),
        ], 'config');
    }
}
