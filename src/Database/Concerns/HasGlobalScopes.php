<?php

namespace Radiate\Database\Concerns;

use Closure;
use InvalidArgumentException;
use Radiate\Database\Builder;

trait HasGlobalScopes
{
    /**
     * The array of global scopes on the model.
     *
     * @var array
     */
    protected static $globalScopes = [];

    /**
     * Register a new global scope on the model.
     *
     * @param \Closure|string $scope
     * @param \Closure|null $implementation
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function addGlobalScope($scope, ?Closure $implementation = null)
    {
        if (is_string($scope) && !is_null($implementation)) {
            return static::$globalScopes[static::class][$scope] = $implementation;
        } elseif ($scope instanceof Closure) {
            return static::$globalScopes[static::class][spl_object_hash($scope)] = $scope;
        }

        throw new InvalidArgumentException('Global scope must be an instance of Closure.');
    }

    /**
     * Determine if a model has a global scope.
     *
     * @param string $scope
     * @return bool
     */
    public static function hasGlobalScope(string $scope)
    {
        return !is_null(static::getGlobalScope($scope));
    }

    /**
     * Get a global scope registered with the model.
     *
     * @param string $scope
     * @return \Closure|null
     */
    public static function getGlobalScope(string $scope)
    {
        return static::$globalScopes[static::class][$scope] ?? null;
    }

    /**
     * Get the global scopes for this class instance.
     *
     * @return array
     */
    public function getGlobalScopes()
    {
        return static::$globalScopes[static::class] ?? [];
    }

    /**
     * Register the global scopes for this builder instance.
     *
     * @param  \Radiate\Database\Builder  $builder
     * @return \Radiate\Database\Builder
     */
    public function registerGlobalScopes(Builder $builder)
    {
        foreach ($this->getGlobalScopes() as $identifier => $scope) {
            $builder->withGlobalScope($identifier, $scope);
        }

        return $builder;
    }
}
