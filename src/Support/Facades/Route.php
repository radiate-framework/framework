<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Radiate\Routing\Route ajax(string $uri, mixed $action) Create an ajax route
 * @method static \Radiate\Routing\Route get(string $uri, mixed $action) Create a GET route
 * @method static \Radiate\Routing\Route post(string $uri, mixed $action) Create a POST route
 * @method static \Radiate\Routing\Route put(string $uri, mixed $action) Create a PUT route
 * @method static \Radiate\Routing\Route patch(string $uri, mixed $action) Create a PATCH route
 * @method static \Radiate\Routing\Route delete(string $uri, mixed $action) Create a DELETE route
 * @method static \Radiate\Routing\Route any(string $uri, mixed $action) Create a route with any method
 * @method static \Radiate\Routing\Route matches(array $methods, string $uri, mixed $action) Create a route matching the given methods
 * @method static void resource(string $uri, string $action, array $methods = ['index', 'show', 'store', 'update', 'destroy']) Create resource routes
 * @method static \Radiate\Routing\Route addRoute(\Radiate\Routing\Route $route) Regsiter the route in the router
 * @method static bool hasGroupStack() Determine if the router currently has a group stack.
 * @method static array getMergedGroupStack() Return the merged group stack
 * @method static \Radiate\Routing\Router middleware(string|array $middleware) Set a group middleware
 * @method static array getMiddleware() Get the group middleware
 * @method static \Radiate\Routing\Router prefix(string $prefix) Set the group prefix
 * @method static string getPrefix() Get the group prefix
 * @method static \Radiate\Routing\Router namespace(string $namespace) Set the group namespace
 * @method static string getNamespace() Get the group namespace
 * @method static void group(\Closure|string $routes) Define a group in the router
 * @method static void dispatch(\Radiate\Http\Request $request) Dispatch the request to the routes
 * @method static void listen(string|string[] $events, mixed $callback) Listen to an event
 *
 * @see \Radiate\Routing\Router
 */

class Route extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'router';
    }
}
