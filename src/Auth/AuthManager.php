<?php

namespace Radiate\Auth;

class AuthManager
{
    /**
     * Attempt a login
     *
     * @param \ArrayAccess|array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt($credentials, bool $remember = false): bool
    {
        return $this->login($credentials, $remember);
    }

    /**
     * Log in
     *
     * @param \ArrayAccess|array $credentials
     * @param bool $remember
     * @return bool
     */
    public function login($credentials, bool $remember = false): bool
    {
        $user = wp_signon([
            'user_login'    => $credentials['username'],
            'user_password' => $credentials['password'],
            'rememberme'    => $remember,
        ]);

        return !is_wp_error($user);
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

    /**
     * Return the auth guard
     *
     * @return self
     */
    public function guard(): self
    {
        return $this;
    }
}
