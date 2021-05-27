<?php

namespace Radiate\Auth;

use WP_User;

class WordPressUserProvider implements UserProvider
{
    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $user = $this->retrieveByCredentials($credentials);

        return $user instanceof WP_User;
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
     * @param \WP_User $user
     * @param bool $remember
     * @return bool
     */
    public function login($user, bool $remember = false): bool
    {
        if (get_user_by('ID', $user->ID)) {
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, $remember);
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
        if ($user = get_user_by('ID', $id)) {
            return $this->login($user, $remember);
        }

        return false;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return \WP_User|false
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = wp_authenticate($credentials['username'], $credentials['password']);

        if (!is_wp_error($user)) {
            return $user;
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
     * @return \WP_User|false
     */
    public function user()
    {
        return $this->check() ? wp_get_current_user() : false;
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
     * @return bool
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
