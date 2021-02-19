<?php

namespace Radiate\Support\Facades;

use RuntimeException;
use Radiate\Foundation\Application;

abstract class Facade
{
    /**
     * The application instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected static $app;

    /**
     * The resolved instances
     *
     * @var array
     */
    protected static $resolvedInstance;

    /**
     * Set the application instance
     *
     * @param \Radiate\Foundation\Application $app
     * @return void
     */
    public static function setFacadeApplication(Application $app): void
    {
        static::$app = $app;
    }

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Resolve the facade instance
     *
     * @param string $name
     * @return mixed
     */
    protected static function resolveFacadeInstance(string $name)
    {
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        if (static::$app) {
            return static::$resolvedInstance[$name] = static::$app[$name];
        }
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances(): void
    {
        static::$resolvedInstance = [];
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance(string $name): void
    {
        unset(static::$resolvedInstance[$name]);
    }

    /**
     * Get the name of the component
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * Dynamically call the facade component
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic(string $method, array $parameters = [])
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$parameters);
    }
}
