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
     * @return \Closure
     */
    public function handle(Request $request)
    {
        return function () use ($request) {
            die($this->runRequestThroughStack($request));
        };
    }

    /**
     * Generate a URL for the route
     *
     * @param \Radiate\Routing\UrlGenerator $url
     * @param array $parameters
     * @return string
     */
    public function generateUrl(UrlGenerator $url, array $parameters = [])
    {
        return $url->ajax($this->uri(), $parameters);
    }
}
