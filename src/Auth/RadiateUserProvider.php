<?php

namespace Radiate\Auth;

use Radiate\Database\Models\User;

class RadiateUserProvider implements UserProvider
{
    /**
     * The user model
     *
     * @var \Radiate\Database\Models\User
     */
    protected $model;

    /**
     * Create the provider instance
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = new $model;
    }

    /**
     * Get the current user attributes
     *
     * @return array
     */
    protected function getCurrentUserAttributes(): array
    {
        return wp_get_current_user()->to_array();
    }

    /**
     * Get a new user model
     *
     * @param array $attributes
     * @return \Radiate\Database\Models\User
     */
    protected function newUserInstance(array $attributes)
    {
        return $this->model->newInstance($attributes, true);
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $user = $this->retrieveByCredentials($credentials);

        return $user instanceof User;
    }

    /**
     * Attempt a login
     *
     * @param \ArrayAccess|array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt($credentials, bool $remember = false): bool
    {
        $user = $this->retrieveByCredentials($credentials);

        return $user ? $this->login($user, $remember) : false;
    }

    /**
     * Log in
     *
     * @param \Radiate\Database\Models\User $user
     * @param bool $remember
     * @return bool
     */
    public function login($user, bool $remember = false): bool
    {
        if ($user = User::find($user->id)) {
            wp_clear_auth_cookie();
            wp_set_current_user($user->id);
            wp_set_auth_cookie($user->id, $remember);
            return true;
        }

        return false;
    }

    /**
     * Log in by the user ID
     *
     * @param int $id
     * @param bool $remember
     * @return bool
     */
    public function loginUsingId(int $id, bool $remember = false): bool
    {
        if ($user = User::find($id)) {
            return $this->login($user, $remember);
        }

        return false;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return \Radiate\Database\Models\User|false
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = wp_authenticate($credentials['username'], $credentials['password']);

        if (!is_wp_error($user)) {
            return $this->newUserInstance($user->to_array());
        }

        return false;
    }

    /**
     * Log out
     *
     * @return void
     */
    public function logout(): void
    {
        wp_logout();
    }

    /**
     * Return the user
     *
     * @return \Radiate\Database\Models\User|false
     */
    public function user()
    {
        $user = $this->newUserInstance($this->getCurrentUserAttributes());

        return $this->check() ? $user : false;
    }

    /**
     * Return the user id
     *
     * @return int|bool
     */
    public function id()
    {
        return ($id = get_current_user_id()) !== 0 ? $id : false;
    }

    /**
     * Determine if the user is logged in
     *
     * @return boolean
     */
    public function check(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Determine if the user is a guest
     *
     * @return bool
     */
    public function guest(): bool
    {
        return !$this->check();
    }
}
