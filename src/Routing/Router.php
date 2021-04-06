<?php

namespace Radiate\Routing;

use Closure;
use Radiate\Events\Dispatcher;
use Radiate\Foundation\Application;
use Radiate\Http\Request;

class Router
{
    /**
     * The events instance
     *
     * @var \Radiate\Foundation\Application
     *
     */
    public $app;

    /**
     * The events instance
     *
     * @var \Radiate\Events\Dispatcher
     */
    protected $events;

    /**
     * The router group stack
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * The current group
     *
     * @var array
     */
    protected $currentGroup = [];

    /**
     * The registered routes
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Create the router instance
     *
     * @param \Radiate\Events\Dispatcher $events
     * @param \Radiate\Foundation\Application $app
     */
    public function __construct(Dispatcher $events, Application $app)
    {
        $this->events = $events;
        $this->app = $app;
    }

    /**
     * Create an ajax route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function ajax(string $uri, $action)
    {
        return $this->addRoute(new AjaxRoute('AJAX', $uri, $action));
    }

    /**
     * Create a GET route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function get(string $uri, $action)
    {
        return $this->addRoute(new RestRoute(['GET', 'HEAD'], $uri, $action));
    }

    /**
     * Create a POST route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function post(string $uri, $action)
    {
        return $this->addRoute(new RestRoute('POST', $uri, $action));
    }

    /**
     * Create a PUT route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function put(string $uri, $action)
    {
        return $this->addRoute(new RestRoute('PUT', $uri, $action));
    }

    /**
     * Create a PATCH route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function patch(string $uri, $action)
    {
        return $this->addRoute(new RestRoute('PATCH', $uri, $action));
    }

    /**
     * Create a DELETE route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function delete(string $uri, $action)
    {
        return $this->addRoute(new RestRoute('DELETE', $uri, $action));
    }

    /**
     * Create a route with any method
     *
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function any(string $uri, $action)
    {
        return $this->addRoute(new RestRoute(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'], $uri, $action));
    }

    /**
     * Create a route matching the given methods
     *
     * @param array $methods
     * @param string $uri
     * @param mixed $action
     * @return \Radiate\Routing\Route
     */
    public function matches(array $methods, string $uri, $action)
    {
        if (in_array('GET', $methods) && !in_array('HEAD', $methods)) {
            $methods[] = 'HEAD';
        }

        return $this->addRoute(new RestRoute($methods, $uri, $action));
    }

    /**
     * Create resource routes
     *
     * @param string $uri
     * @param string $action
     * @param array $methods
     * @return void
     */
    public function resource(string $uri, string $action, array $methods = ['index', 'show', 'store', 'update', 'destroy'])
    {
        $this->prefix($uri)->group(function () use ($action, $methods) {
            if (in_array('index', $methods)) {
                $this->get('/', [$action, 'index']);
            }
            if (in_array('show', $methods)) {
                $this->get('{id}', [$action, 'show']);
            }
            if (in_array('store', $methods)) {
                $this->post('/', [$action, 'store']);
            }
            if (in_array('update', $methods)) {
                $this->matches(['PUT', 'PATCH'], '{id}', [$action, 'update']);
            }
            if (in_array('destroy', $methods)) {
                $this->delete('{id}', [$action, 'destroy']);
            }
        });
    }

    /**
     * Regsiter the route in the router
     *
     * @param \Radiate\Routing\Route $route
     * @return \Radiate\Routing\Route
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;

        if ($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoRoute($route);
        }

        return $route;
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return !empty($this->groupStack);
    }

    /**
     * Merge the group stack with the controller action.
     *
     * @param  \Radiate\Routing\Route  $route
     * @return void
     */
    protected function mergeGroupAttributesIntoRoute($route)
    {
        $route->setGroupAttributes($this->getMergedGroupStack());
    }

    /**
     * Return the merged group stack
     *
     * @return array
     */
    public function getMergedGroupStack()
    {
        return [
            'middleware' => $this->getMiddleware(),
            'prefix'     => $this->getPrefix(),
            'namespace'  => $this->getNamespace(),
        ];
    }

    /**
     * Set a group middleware
     *
     * @param string|array $middleware
     * @return self
     */
    public function middleware($middleware)
    {
        $this->currentGroup['middleware'] = (array) $middleware;

        return $this;
    }

    /**
     * Get the group middleware
     *
     * @return array
     */
    public function getMiddleware()
    {
        $middleware = [];

        foreach (array_column($this->groupStack, 'middleware') as $aliases) {
            foreach ($aliases as $alias) {
                $middleware[] = $alias;
            }
        };

        return array_unique($middleware);
    }

    /**
     * Set the group prefix
     *
     * @param string $prefix
     * @return self
     */
    public function prefix(string $prefix)
    {
        $this->currentGroup['prefix'] = trim($prefix, '/');

        return $this;
    }

    /**
     * Get the group prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return implode('/', array_column($this->groupStack, 'prefix'));
    }

    /**
     * Set the group namespace
     *
     * @param string $namespace
     * @return self
     */
    public function namespace(string $namespace)
    {
        $this->currentGroup['namespace'] = trim($namespace, '/');

        return $this;
    }

    /**
     * Get the group namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return implode('/', array_column($this->groupStack, 'namespace'));
    }

    /**
     * Define a group in the router
     *
     * @param \Closure|string $callback
     * @return void
     */
    public function group($routes)
    {
        $this->groupStack[] = $this->currentGroup;
        $this->currentGroup = [];

        $this->loadRoutes($routes);

        array_pop($this->groupStack);
    }

    /**
     * Load the provided routes.
     *
     * @param \Closure|string $routes
     * @return void
     */
    protected function loadRoutes($routes)
    {
        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            $router = $this;

            require $routes;
        }
    }

    /**
     * Dispatch the request to the routes
     *
     * @param \Radiate\Http\Request $request
     * @return void
     */
    public function dispatch(Request $request)
    {
        foreach ($this->routes as $route) {
            $route->setRouter($this)
                ->setContainer($this->app)
                ->dispatch($request);
        }
    }

    /**
     * Listen to an event
     *
     * @param string|string[] $events
     * @param mixed $callback
     * @return void
     */
    public function listen($events, $callback)
    {
        $this->events->listen($events, $callback);
    }
}
