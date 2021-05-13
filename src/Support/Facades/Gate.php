<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Radiate\Auth\Access\Gate authorize(string $ability, array|mixed $arguments = []) Determine if the given ability should be granted for the current user.
 * @method static bool inspect(string $ability, array|mixed $arguments = []) Inspect the user for the given ability.
 * @method static \Radiate\Auth\Access\Gate define(string $ability, callable|string $callback) Define a new ability.
 * @method static \Radiate\Auth\Access\Gate forUser(\Radiate\Database\Models\User|mixed $user) Get a gate instance for the given user.
 * @method static \Radiate\Auth\Access\Gate policy(string $class, string $policy) Define a policy class for a given class type.
 * @method static array abilities() Get all of the defined abilities.
 * @method static array policies() Get all of the defined policies.
 * @method static bool allows(string $ability, array|mixed $arguments = []) Determine if the given ability should be granted for the current user.
 * @method static bool any(array|string $abilities, array|mixed $arguments = []) Determine if any one of the given abilities should be granted for the current user.
 * @method static bool none(array|string $abilities, array|mixed $arguments = []) Determine if all of the given abilities should be denied for the current user.
 * @method static bool check(array|string $abilities, array|mixed $arguments = []) Determine if all of the given abilities should be granted for the current user.
 * @method static bool denies(string $ability, array|mixed $arguments = []) Determine if the given ability should be denied for the current user.
 * @method static bool has(string $ability): Determine if a given ability has been defined.
 * @method static bool raw(string $ability, array|mixed $arguments = []) Get the raw result from the authorization callback.
 * @method static bool hasCoreCapability(\Radiate\Database\Models\User $user, string $ability, array $arguments = []) Determine if the user has a core capability
 *
 * @see \Radiate\Auth\Access\Gate
 */
class Gate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'gate';
    }
}
