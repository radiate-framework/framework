<?php

namespace Radiate\Auth\Access;

use Closure;
use Radiate\Auth\Contracts\Authenticatable;
use Radiate\Database\Models\User;
use Radiate\Foundation\Application;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Support\Arr;
use Radiate\Support\Collection;
use Radiate\Support\Str;

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

        if (!$this->has($ability) && !$this->hasPolicy($arguments)) {
            return $result;
        }

        return $this->callAuthCallback($user, $ability, $arguments);
    }

    /**
     * Determine if the first argument is a policy
     *
     * @param array $arguments
     * @return boolean
     */
    protected function hasPolicy(array $arguments)
    {
        return isset($arguments[0]) && !is_null($this->getPolicyFor($arguments[0]));
    }

    /**
     * Resolve and call the appropriate authorization callback.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|null  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool
     */
    protected function callAuthCallback(?Authenticatable $user, string $ability, array $arguments)
    {
        $callback = $this->resolveAuthCallback($user, $ability, $arguments);

        return $callback($user, ...$arguments);
    }

    /**
     * Resolve the callable for the given ability and arguments.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|null  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return callable
     */
    protected function resolveAuthCallback(?Authenticatable $user, string $ability, array $arguments)
    {
        if (
            isset($arguments[0]) &&
            !is_null($policy = $this->getPolicyFor($arguments[0])) &&
            $callback = $this->resolvePolicyCallback($user, $ability, $arguments, $policy)
        ) {
            return $callback;
        }

        if ($this->has($ability)) {
            return $this->abilities[$ability];
        }

        return function () {
            //
        };
    }

    /**
     * Resolve the callback for a policy check.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @param  mixed  $policy
     * @return bool|callable
     */
    protected function resolvePolicyCallback($user, $ability, array $arguments, $policy)
    {
        if (!is_callable([$policy, $this->formatAbilityToMethod($ability)])) {
            return false;
        }

        return function () use ($user, $ability, $arguments, $policy) {
            $method = $this->formatAbilityToMethod($ability);

            return $this->callPolicyMethod($policy, $method, $user, $arguments);
        };
    }

    /**
     * Format the policy ability into a method name.
     *
     * @param  string  $ability
     * @return string
     */
    protected function formatAbilityToMethod(string $ability)
    {
        return Str::camel($ability);
    }

    /**
     * Call the appropriate method on the given policy.
     *
     * @param  mixed  $policy
     * @param  string  $method
     * @param  \Radiate\Auth\Contracts\Authenticatable|null  $user
     * @param  array  $arguments
     * @return mixed
     */
    protected function callPolicyMethod($policy, $method, $user, array $arguments)
    {
        // If this first argument is a string, that means they are passing a class name
        // to the policy. We will remove the first argument from this argument array
        // because this policy already knows what type of models it can authorize.
        if (isset($arguments[0]) && is_string($arguments[0])) {
            array_shift($arguments);
        }

        if (!is_callable([$policy, $method])) {
            return;
        }

        return $policy->{$method}($user, ...$arguments);
    }

    /**
     * Get a policy instance for a given class.
     *
     * @param  object|string  $class
     * @return mixed
     */
    public function getPolicyFor($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!is_string($class)) {
            return;
        }

        if (isset($this->policies[$class])) {
            return $this->resolvePolicy($this->policies[$class]);
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolvePolicy($policy);
            }
        }
    }

    /**
     * Build a policy class instance of the given type.
     *
     * @param  object|string  $class
     * @return mixed
     */
    public function resolvePolicy($class)
    {
        return $this->app->make($class);
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
     * @param  \Radiate\Auth\Contracts\Authenticatable  $user
     * @return static
     */
    public function forUser(Authenticatable $user)
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
