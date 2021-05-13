<?php

namespace Radiate\Auth\Access;

use Closure;
use Radiate\Database\Models\User;
use Radiate\Foundation\Application;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Support\Arr;
use Radiate\Support\Collection;

class Gate
{
    /**
     * The app instance.
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * The user resolver callable.
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * All of the defined abilities.
     *
     * @var array
     */
    protected $abilities = [];

    /**
     * All of the defined policies.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Create a new gate instance.
     *
     * @param  \Radiate\Foundation\Application  $app
     * @param  \Closure  $userResolver
     * @return void
     */
    public function __construct(
        Application $app,
        Closure $userResolver,
        array $abilities = [],
        array $policies = []
    ) {
        $this->app = $app;
        $this->userResolver = $userResolver;
        $this->abilities = $abilities;
        $this->policies = $policies;
    }

    /**
     * Define a new ability.
     *
     * @param  string  $ability
     * @param  callable|string  $callback
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function define(string $name, Closure $callback)
    {
        $this->abilities[$name] = $callback;

        return $this;
    }

    /**
     * Determine if all of the given abilities should be granted for the current user.
     *
     * @param  array|string  $abilities
     * @param  array  $arguments
     * @return bool
     */
    public function check($abilities, array $arguments = [])
    {
        return Collection::make($abilities)->every(function ($ability) use ($arguments) {
            return $this->inspect($ability, $arguments);
        });
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function allows(string $ability, $arguments = [])
    {
        return $this->check($ability, Arr::wrap($arguments));
    }

    /**
     * Determine if any one of the given abilities should be granted for the current user.
     *
     * @param  array|string  $abilities
     * @param  array  $arguments
     * @return bool
     */
    public function any($abilities, array $arguments = [])
    {
        return Collection::make($abilities)->contains(function ($ability) use ($arguments) {
            return $this->inspect($ability, $arguments);
        });
    }

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function denies(string $ability, $arguments = [])
    {
        return !$this->allows($ability, Arr::Wrap($arguments));
    }

    /**
     * Determine if all of the given abilities should be denied for the current user.
     *
     * @param  array|string  $abilities
     * @param  array  $arguments
     * @return bool
     */
    public function none($abilities, array $arguments = [])
    {
        return !$this->any($abilities, $arguments);
    }

    /**
     * Inspect the user for the given ability.
     *
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool
     */
    public function inspect(string $ability, array $arguments = [])
    {
        return $this->raw($ability, $arguments);
    }

    /**
     * Get the raw result from the authorization callback.
     *
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool
     */
    public function raw(string $ability, array $arguments = [])
    {
        $user = $this->resolveUser() ?: null;

        $result = $user && $this->hasCoreCapability($user, $ability, $arguments);

        if (!$callback = $this->abilities[$ability]) {
            return $result;
        }

        return $callback($user, ...$arguments);
    }

    /**
     * Determine if the user has a core capability
     *
     * @param \Radiate\Database\Models\User $user
     * @param string $ability
     * @param array $arguments
     * @return boolean
     */
    public function hasCoreCapability(User $user, string $ability, array $arguments = [])
    {
        return user_can($user->getKey(), $ability, ...$arguments);
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return static
     *
     * @throws \Radiate\Foundation\Http\Exceptions\HttpResponseException
     */
    public function authorize(string $ability, $arguments = [])
    {
        if (!$this->inspect($ability, Arr::wrap($arguments))) {
            throw new HttpResponseException('This action is unauthorized.', 403);
        }

        return $this;
    }

    /**
     * Resolve the user from the user resolver.
     *
     * @return mixed
     */
    protected function resolveUser()
    {
        return call_user_func($this->userResolver);
    }

    /**
     * Get a gate instance for the given user.
     *
     * @param  \Radiate\Database\Models\User  $user
     * @return static
     */
    public function forUser(User $user)
    {
        $callback = function () use ($user) {
            return $user;
        };

        return new static(
            $this->app,
            $callback,
            $this->abilities,
            $this->policies
        );
    }

    /**
     * Determine if a given ability has been defined.
     *
     * @param  string  $ability
     * @return bool
     */
    public function has(string $ability)
    {
        return isset($this->abilities[$ability]);
    }

    /**
     * Get all of the defined abilities.
     *
     * @return array
     */
    public function abilities()
    {
        return $this->abilities;
    }

    /**
     * Get all of the defined policies.
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }

    /**
     * Define a policy class for a given class type.
     *
     * @param  string  $class
     * @param  string  $policy
     * @return static
     */
    public function policy(string $class, string $policy)
    {
        $this->policies[$class] = $policy;

        return $this;
    }
}
