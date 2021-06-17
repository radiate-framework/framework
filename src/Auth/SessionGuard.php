<?php

namespace Radiate\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Traits\Macroable;
use Radiate\Auth\Contracts\UserProvider;
use WP_User;

class SessionGuard implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * Create a new authentication guard.
     *
     * @param  \Radiate\Auth\Contracts\UserProvider  $provider
     * @return void
     */
    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return is_user_logged_in();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        return ($id = get_current_user_id()) !== 0 ? $id : null;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Radiate\Auth\Contracts\Authenticatable|\WP_User|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        return $this->user = $this->provider->retrieveById($this->id());
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public function attempt(array $credentials = [], bool $remember = false)
    {
        if ($user = $this->provider->retrieveByCredentials($credentials)) {
            $this->login($user, $remember);

            return true;
        }

        return false;
    }

    /**
     * Log a user into the application.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @param  bool  $remember
     * @return void
     */
    public function login($user, bool $remember = false)
    {
        $id = $this->getAuthIdentifier($user);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($id, $remember);

        $this->setUser($user);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  int  $id
     * @param  bool  $remember
     * @return bool
     */
    public function loginUsingId(int $id, bool $remember = false)
    {
        if ($user = $this->provider->retrieveById($id)) {
            $this->login($user, $remember);

            return true;
        }

        return false;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $this->user = null;

        wp_logout();
    }

    /**
     * Set the current user.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @return static
     */
    public function setUser($user)
    {
        $this->user = $user;

        wp_set_current_user($this->getAuthIdentifier($user));

        return $this;
    }
}
