<?php

namespace Radiate\Auth;

interface UserProvider
{
    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = []);

    /**
     * Attempt a login
     *
     * @param \ArrayAccess|array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt($credentials, bool $remember = false): bool;

    /**
     * Log in
     *
     * @param mixed $user
     * @param bool $remember
     * @return bool
     */
    public function login($user, bool $remember = false): bool;

    /**
     * Log in by the user ID
     *
     * @param int $id
     * @param bool $remember
     * @return bool
     */
    public function loginUsingId(int $id, bool $remember = false): bool;

    /**
     * Log out
     *
     * @return void
     */
    public function logout(): void;

    /**
     * Return the user
     *
     * @return mixed|false
     */
    public function user();

    /**
     * Return the user id
     *
     * @return int|bool
     */
    public function id();

    /**
     * Determine if the user is logged in
     *
     * @return bool
     */
    public function check(): bool;

    /**
     * Determine if the user is a guest
     *
     * @return bool
     */
    public function guest(): bool;
}
