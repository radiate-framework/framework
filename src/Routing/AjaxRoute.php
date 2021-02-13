<?php

namespace Radiate\Routing;

use Radiate\Http\Request;

class AjaxRoute extends Route
{
    /**
     * Dispatch the request to the route
     *
     * @param \Radiate\Http\Request $request
     * @return void
     */
    public function dispatch(Request $request)
    {
        $uri = $this->uri();

        $this->router->listen(
            ['wp_ajax_' . $uri, 'wp_ajax_nopriv_' . $uri],
            $this->handle($request)
        );
    }

    /**
     * Dispatch the route
     *
     * @param \Radiate\Http\Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        return function () use ($request) {
            die($this->runRequestThroughStack($request));
        };
    }
}
