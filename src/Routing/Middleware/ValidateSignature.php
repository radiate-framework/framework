<?php

namespace Radiate\Routing\Middleware;

use Closure;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Request;

class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param \Radiate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasValidSignature()) {
            return $next($request);
        }

        throw new HttpResponseException('Invalid signature.', 403);
    }
}
