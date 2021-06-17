<?php

namespace Radiate\Auth;

use Radiate\Auth\Contracts\UserProvider;
use WP_User;

trait GuardHelpers
{
    /**
     * The currently authenticated user.
     *
     * @var \Radiate\Auth\Contracts\Authenticatable|\WP_User
     */
    protected $user;

    /**
     * The user provider implementation.
     *
     * @var \Radiate\Auth\Contracts\UserProvider
     */
    protected $provider;

    /**
     * Determine if the current user is authenticated. If not, throw an exception.
     *
     * @return \Radiate\Auth\Contracts\Authenticatable|\WP_User
     *
     * @throws \Radiate\Auth\AuthenticationException
     */
    public function authenticate()
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException();
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return !is_null($this->user);
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        if ($user = $this->user()) {
            return $this->getAuthIdentifier($user);
        }
    }

    /**
     * Set the current user.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return \Radiate\Auth\Contracts\UserProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param  \Radiate\Auth\Contracts\UserProvider  $provider
     * @return void
     */
    public function setProvider(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get the users' auth identifier
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @return int|string|null
     */
    protected function getAuthIdentifier($user)
    {
        return $user instanceof WP_User ? $user->ID : $user->getAuthIdentifier();
    }
}
