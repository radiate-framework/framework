<?php

namespace Radiate\Auth;

use Radiate\Auth\Contracts\UserProvider;

class RadiateUserProvider implements UserProvider
{
    /**
     * The user model
     *
     * @var \Radiate\Database\Model
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
     * Retrieve a user by their unique identifier.
     *
     * @param  int|string|null  $identifier
     * @return \Radiate\Auth\Contracts\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->model->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Radiate\Auth\Contracts\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = wp_authenticate($credentials['email'], $credentials['password']);

        if (!is_wp_error($user)) {
            return $this->model->newInstance($user->to_array());
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Radiate\Auth\Contracts\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials($user, array $credentials)
    {
        return wp_check_password(
            $credentials['password'],
            $user->getAuthPassword(),
            $user->getAuthIdentifier()
        );
    }
}
