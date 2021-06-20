<?php

namespace Radiate\Auth\Middleware;

use Closure;
use Radiate\Auth\AuthManager;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Request;

class AuthenticateWithBasicAuth
{
    /**
     * The auth mananger
     *
     * @var \Radiate\Auth\AuthManager
     */
    protected $auth;

    /**
     * Create the middeware
     *
     * @param \Radiate\Auth\AuthManager $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Radiate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @param string|null $field
     * @return mixed
     *
     * @throws \Radiate\Foundation\Http\Exceptions\HttpResponseException
     */
    public function handle(Request $request, Closure $next, $guard = null, $field = null)
    {
        if ($this->auth->guard($guard)->basic($field ?: 'email')) {
            return $next($request);
        }

        header('WWW_Authenticate: Basic');

        throw new HttpResponseException('Unauthorised.', 401);
    }
}