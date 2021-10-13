<?php

namespace Radiate\Routing\Middleware;

use Closure;
use Radiate\Auth\AuthorizationException;
use Radiate\Http\Request;

class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param \Radiate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     *
     * @throws \Radiate\Auth\AuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasValidSignature()) {
            return $next($request);
        }

        throw new AuthorizationException('Invalid signature.');
    }
}
