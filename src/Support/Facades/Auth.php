<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Illuminate\Contracts\Auth\Guard guard(string|null $name = null) Attempt to get the guard from the local cache.
 * @method static string getDefaultDriver() Get the default authentication driver name.
 * @method static void shouldUse(string $name) Set the default guard driver the factory should serve.
 * @method static void setDefaultDriver(string $name) Set the default authentication driver name.
 * @method static \Closure userResolver() Get the user resolver callback.
 * @method static \Radiate\Auth\AuthManager resolveUsersUsing(\Closure $userResolver) Set the callback to be used to resolve users.
 * @method static \Radiate\Auth\AuthManager extend(string $driver, \Closure $callback) Register a custom driver creator Closure.
 * @method static \Radiate\Auth\AuthManager provider(string $name, \Closure $callback) Register a custom provider creator Closure.
 * @method static bool hasResolvedGuards() Determines if any guards have already been resolved.
 * @method static \Radiate\Auth\AuthManager forgetGuards() Forget all of the resolved guard instances.
 * @method static bool attempt(array $credentials = [], bool $remember = false) Attempt to authenticate a user using the given credentials.
 * @method static bool once(array $credentials = []) Log a user into the application without sessions or cookies.
 * @method static void login(\Radiate\Auth\Contracts\Authenticatable|\WP_User $user, bool $remember = false) Log a user into the application.
 * @method static bool loginUsingId(mixed $id, bool $remember = false) Log the given user ID into the application.
 * @method static bool onceUsingId(mixed $id) Log the given user ID into the application without sessions or cookies.
 * @method static void logout() Log the user out of the application. *
 * @method static \Radiate\Auth\Contracts\Authenticatable|\WP_User authenticate() Determine if the current user is authenticated. If not, throw an exception.
 * @method static bool hasUser() Determine if the guard has a user instance.
 * @method static bool check() Determine if the current user is authenticated.
 * @method static bool guest() Determine if the current user is a guest.
 * @method static int|string|null id() Get the ID for the currently authenticated user.
 * @method static Illuminate\Contracts\Auth\Guard setUser(\Radiate\Auth\Contracts\Authenticatable|\WP_User $user) Set the current user.
 * @method static \Radiate\Auth\Contracts\UserProvider getProvider() Get the user provider used by the guard.
 * @method static void setProvider(\Radiate\Auth\Contracts\UserProvider $provider) Set the user provider used by the guard.
 * @method static bool basic(string $field = 'email') Attempt to authenticate using HTTP Basic Auth.
 * @method static bool onceBasic(string $field = 'email') Perform a stateless HTTP Basic login attempt.
 *
 * @see \Radiate\Auth\AuthManager
 * @see \Radiate\Auth\Contracts\Guard
 * @see \Radiate\Auth\Contracts\StatefulGuard
 */
class Auth extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'auth';
    }
}
