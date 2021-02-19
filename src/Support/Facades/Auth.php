<?php

namespace Radiate\Support\Facades;

/**
 * @method static bool attempt(array $credentials, bool $remember = false) Attempt a login
 * @method static bool login(array $credentials, bool $remember = false) Log in
 * @method static void logout() Log out
 * @method static \WP_User|false user() Return the user
 * @method static int|bool id() Return the user id
 * @method static bool check() Determine if the user is logged in
 * @method static bool guest() Determine if the user is a guest
 * @method static \Radiate\Auth\AuthManager guard() Return the auth guard
 *
 * @see \Radiate\Auth\AuthManager
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
