<?php

namespace Radiate\Auth;

use Illuminate\Support\Traits\Macroable;
use Radiate\Auth\Contracts\Guard;
use Radiate\Auth\Contracts\UserProvider;
use Radiate\Http\Request;
use Radiate\JWT\Manager;

class JwtGuard implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * The jwt manager
     *
     * @var \Radiate\JWT\Manager
     */
    protected $jwt;

    /**
     * The request
     *
     * @var \Radiate\Auth\Contracts\UserProvider
     */
    protected $provider;

    /**
     * The request
     *
     * @var \Radiate\Http\Request
     */
    protected $request;

    /**
     * Create a new authentication guard.
     *
     * @param  \Radiate\JWT\Manager  $jwt
     * @param  \Radiate\Auth\Contracts\UserProvider  $provider
     * @param  \Radiate\Http\Request  $request
     * @return void
     */
    public function __construct(Manager $jwt, UserProvider $provider, Request $request)
    {
        $this->jwt = $jwt;
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Radiate\Auth\Contracts\Authenticatable|\WP_User|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($payload = $this->jwt->payload($this->getRequest())) {
            return $this->setUser($this->provider->retrieveById($payload->sub));
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Get the current request instance.
     *
     * @return \Radiate\Http\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::capture();
    }
}
