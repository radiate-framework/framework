<?php

namespace Radiate\Foundation\Http\Middleware;

use Closure;
use Radiate\Http\Request;

class ConvertEmptyStringsToNull
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
        return $next($this->transform($request));
    }

    /**
     * Trim whitespace from the request strings
     *
     * @param \Radiate\Http\Request $request
     * @return \Radiate\Http\Request
     */
    protected function transform(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $request[$key] = $value === '' ? null : $value;
        }

        return $request;
    }
}
