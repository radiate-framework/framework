<?php

namespace Radiate\Auth\Middleware;

use Closure;
use Radiate\Auth\AuthManager;
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
     * @throws \Radiate\Auth\AuthorizationException
     */
    public function handle(Request $request, Closure $next, ?string $guard = null, ?string $field = null)
    {
        $this->auth->guard($guard)->basic($field ?: 'email');

        return $next($request);
    }
}
