<?php

namespace Radiate\Auth\Contracts;

interface UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  int|string|null  $identifier
     * @return \Radiate\Auth\Contracts\Authenticatable|\WP_User|null
     */
    public function retrieveById($identifier);

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Radiate\Auth\Contracts\Authenticatable|\WP_User|null
     */
    public function retrieveByCredentials(array $credentials);

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable|\WP_User  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials($user, array $credentials);
}
