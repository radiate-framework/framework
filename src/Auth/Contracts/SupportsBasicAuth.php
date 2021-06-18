<?php

namespace Radiate\Auth\Contracts;

interface SupportsBasicAuth
{
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param  string  $field
     * @return bool
     */
    public function basic(string $field = 'email');

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param  string  $field
     * @return bool
     */
    public function onceBasic(string $field = 'email');
}
