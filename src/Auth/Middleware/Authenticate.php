<?php

namespace Radiate\Auth\Middleware;

use Closure;
use Radiate\Auth\AuthenticationException;
use Radiate\Auth\AuthManager;
use Radiate\Http\Request;

class Authenticate
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
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Radiate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Request\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Radiate\Auth\AuthenticationException
     */
    protected function authenticate(Request $request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Request\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Radiate\Auth\AuthenticationException
     */
    protected function unauthenticated(Request $request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Radiate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request)
    {
        //
    }
}
