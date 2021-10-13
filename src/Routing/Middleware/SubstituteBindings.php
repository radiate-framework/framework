<?php

namespace Radiate\Routing\Middleware;

use Closure;
use Radiate\Database\Model;
use Radiate\Database\ModelNotFoundException;
use Radiate\Foundation\Application;
use Radiate\Http\Request;
use Radiate\Routing\Route;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;

class SubstituteBindings
{
    /**
     * The app instance
     *
     * @var \Radiate\Foudation\Application
     */
    protected $app;

    /**
     * Create the middleware
     *
     * @param \Radiate\Foudation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Radiate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->substituteBindings($request->route());

        return $next($request);
    }

    /**
     * Substitute bindings
     *
     * @param \Radiate\Routing\Route $route
     * @return void
     */
    public function substituteBindings(Route $route)
    {
        $signatureParameters = $this->routeSignatureParameters($route->action());

        $parameters = $this->getParameterNames($signatureParameters);

        foreach ($route->parameters() as $key => $value) {
            if (isset($parameters[$key])) {
                $route->setParameter($key, $this->resolveBinding($parameters[$key], $value));
            }
        }
    }

    /**
     * Resolve the route parameter bindings
     *
     * @param string $model
     * @param mixed $value
     * @return \Radiate\Database\Model|mixed
     *
     * @throws \Radiate\Database\ModelNotFoundException
     */
    public function resolveBinding(string $model, $value)
    {
        $instance = $this->app->make($model);

        if ($instance instanceof Model) {
            if (!$return = $instance->where($instance->getRouteKeyName(), $value)->first()) {
                throw new ModelNotFoundException($model);
            }

            return $return;
        }

        return $value;
    }

    /**
     * Reflect the action
     *
     * @param mixed $action
     * @return \ReflectionParameter[]
     */
    public function routeSignatureParameters($action)
    {
        $action = is_array($action)
            ? new ReflectionMethod(...$action)
            : new ReflectionFunction($action);

        return $action->getParameters();
    }

    /**
     * Return an array of named parameters
     *
     * @param array $parameters
     * @return array
     */
    public function getParameterNames(array $parameters)
    {
        $return = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $return[$parameter->getName()] = $type->getName();
            }
        }

        return $return;
    }
}
