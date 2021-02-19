<?php

namespace Radiate\Support\Facades;

/**
 * @method static string basePath(string $path = null) Get the app base path
 * @method static void register(string $provider) Register a service provider
 * @method static \Radiate\Foundation\Application middleware(array $middleware) Add a global middleware to the app
 * @method static \Radiate\Foundation\Application routeMiddleware(array $middleware) Add a global middleware to the app
 * @method static array getRouteMiddleware() Get the route middleware
 * @method static void boot() Boot the application
 * @method static string getNamespace() Get the app namespace
 * @method static bool runningInConsole() Determine if the app is running in the console
 * @method static string renderException(\Radiate\Http\Request $request, \Throwable $e) Render an HTTP exception
 * @method static string|bool environment(string|array|null $environments = null) Get or check the current application environment.
 * @method static bool isLocal() Determine if the app is in a local environment
 * @method static bool isDevelopment() Determine if the app is in a development environment
 * @method static bool isStaging() Determine if the app is in a staging environment
 * @method static bool isProduction() Determine if the app is in a production environment
 * @method static void instance(string $abstract, mixed $concrete) Register an existing instance in the container.
 * @method static void bind(string $abstract, \Closure $concrete, bool $shared = false) Register a binding with the container.
 * @method static void singleton(string $abstract, \Closure $concrete) Register a shared binding with the container.
 * @method static bool has(string $abstract) Determine if the abstract is bound to the container
 * @method static mixed get(string $abstract) Resolve an instance
 * @method static \Radiate\Foundation\Application getInstance() Get the container instance
 * @method static \Radiate\Foundation\Application setInstance(\Illuminate\Container\Container $container = null) Set the container instance
 *
 * @see \Radiate\Foundation\Application
 */
class App extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'app';
    }
}
