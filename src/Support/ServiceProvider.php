<?php

namespace Radiate\Support;

use Radiate\Foundation\Application;

abstract class ServiceProvider
{
    /**
     * The app instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * The files to publish
     *
     * @var array
     */
    protected static $publishes = [];

    /**
     * Create the provider
     *
     * @param \Radiate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register the provider
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * An array of files/directories to publish
     *
     * @param array $files
     * @param string $tag
     * @return void
     */
    public function publishes(array $files, string $tag = 'all')
    {
        static::$publishes[$tag][static::class] = $files;
    }

    /**
     * Get the paths to publish
     *
     * @param string|null $provider
     * @param string|null $tag
     * @return array
     */
    public static function pathsToPublish(?string $provider = null, ?string $tag = null)
    {
        if ($provider && $tag) {
            return static::$publishes[$tag][$provider];
        } elseif ($provider) {
            return array_merge(...array_column(static::$publishes, $provider));
        } elseif ($tag) {
            return array_merge(...array_values(static::$publishes[$tag]));
        } else {
            return array_merge(...array_values(array_merge_recursive(...array_values(static::$publishes))));
        }
    }

    /**
     * Register the commands
     *
     * @param array $commands
     * @return void
     */
    public function commands(array $commands)
    {
        if ($this->app->runningInConsole()) {
            foreach ($commands as $command) {
                $this->app['console']->make($command);
            }
        }
    }
}
