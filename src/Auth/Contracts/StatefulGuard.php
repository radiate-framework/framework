<?php

namespace Radiate\Auth\Contracts;

interface StatefulGuard extends Guard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public function attempt(array $credentials = [], bool $remember = false);

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function once(array $credentials = []);

    /**
     * Log a user into the application.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @param  bool  $remember
     * @return void
     */
    public function login($user, bool $remember = false);

    /**
     * Log the given user ID into the application.
     *
     * @param  int|string|null  $id
     * @param  bool  $remember
     * @return bool
     */
    public function loginUsingId($id, bool $remember = false);

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  int|string|null  $id
     * @return bool
     */
    public function onceUsingId($id);

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout();
}
