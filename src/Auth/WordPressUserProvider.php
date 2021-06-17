<?php

namespace Radiate\Auth;

use Radiate\Auth\Contracts\UserProvider;
use WP_User;

class WordPressUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  int|string|null  $identifier
     * @return \WP_User|null
     */
    public function retrieveById($identifier)
    {
        $user = new WP_User($identifier);

        return $user->id !== 0 ? $user : null;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \WP_User|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = wp_authenticate($credentials['email'], $credentials['password']);

        if (!is_wp_error($user)) {
            return $user;
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \WP_User  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials($user, array $credentials)
    {
        return wp_check_password(
            $credentials['password'],
            $user->user_password,
            $user->ID
        );
    }
}
