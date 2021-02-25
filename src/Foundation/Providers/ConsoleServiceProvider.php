<?php

namespace Radiate\Foundation\Providers;

use Radiate\Console\Command;
use Radiate\Console\Factory;
use Radiate\Support\ServiceProvider;
use Radiate\Support\Str;
use ReflectionClass;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The core commands to register
     *
     * @var array
     */
    protected $commands = [
        \Radiate\Foundation\Console\DownCommand::class,
        \Radiate\Foundation\Console\EnvironmentCommand::class,
        \Radiate\Foundation\Console\ListCommand::class,
        \Radiate\Foundation\Console\MakeCommand::class,
        \Radiate\Foundation\Console\MakeProvider::class,
        \Radiate\Foundation\Console\MakeRequest::class,
        \Radiate\Foundation\Console\UpCommand::class,
        \Radiate\Foundation\Console\VendorPublish::class,
    ];

    /**
     * Register the services
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('console', function ($app) {
            return new Factory($app);
        });
    }

    /**
     * Boot the services
     *
     * @return void
     */
    public function boot()
    {
        $this->load($this->app->basePath('app/Console'));

        $this->commands($this->commands);
    }

    /**
     * Get the file paths to looad
     *
     * @param string|array $paths
     * @return void
     */
    protected function filePaths($paths)
    {
        $paths = array_unique((array) $paths);

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        return $paths;
    }

    /**
     * Register all of the custom commands in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function load($paths)
    {
        if (empty($paths = $this->filePaths($paths))) {
            return;
        }

        foreach ($this->app['files']->allFiles($paths) as $file) {
            $command = $this->resolveClassName($file->getRealPath());

            if ($this->commandClass($command)) {
                $this->app['console']->make($command);
            }
        }
    }

    /**
     * Resolve the file classname
     *
     * @param string $fileName
     * @return string
     */
    protected function resolveClassName(string $fileName)
    {
        $appPath = realpath($this->app->basePath('app')) . DIRECTORY_SEPARATOR;

        $class = str_replace(['/', '.php'], ['\\', ''], Str::after($fileName, $appPath));

        return $this->app->getNamespace() . $class;
    }

    /**
     * Determine if the command is a subclass of Command
     *
     * @param string $command
     * @return boolean
     */
    protected function commandClass(string $command)
    {
        return is_subclass_of($command, Command::class) &&
            !(new ReflectionClass($command))->isAbstract();
    }
}
