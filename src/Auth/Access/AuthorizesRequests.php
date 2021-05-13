<?php

namespace Radiate\Auth\Access;

use Radiate\Support\Facades\Gate;

trait AuthorizesRequests
{
    /**
     * Authorize a given action for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \Radiate\Auth\Access\Gate
     *
     * @throws \Radiate\Foundation\Http\Exceptions\HttpResponseException
     */
    public function authorize(string $ability, $arguments = [])
    {
        return Gate::authorize($ability, $arguments);
    }

    /**
     * Authorize a given action for a user.
     *
     * @param  \Radiate\Database\Models\User|mixed  $user
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \Radiate\Auth\Access\Gate
     *
     * @throws \Radiate\Foundation\Http\Exceptions\HttpResponseException
     */
    public function authorizeForUser($user, string $ability, $arguments = [])
    {
        return Gate::forUser($user)->authorize($ability, $arguments);
    }
}
