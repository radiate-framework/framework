<?php

namespace Radiate\Routing;

use Radiate\Http\Request;
use WP_REST_Request;

class RestRoute extends Route
{
    /**
     * Dispatch the request to the route
     *
     * @param \Radiate\Http\Request $request
     * @return void
     */
    public function dispatch(Request $request)
    {
        $this->router->listen('rest_api_init', function () use ($request) {
            register_rest_route($this->namespace(), $this->parseUri($this->uri()), [
                'methods'             => $this->methods(),
                'callback'            => $this->handle($request),
                'permission_callback' => '__return_true',
            ]);
        });
    }

    /**
     * Dispatch the route
     *
     * @param \Radiate\Http\Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        return function (WP_REST_Request $wpRequest) use ($request) {
            die($this->runRequestThroughStack(
                $request->merge($parameters = $wpRequest->get_url_params()),
                array_values($parameters)
            ));
        };
    }

    /**
     * Parse the uri into a WordPress compatible format.
     *
     * @param string $uri
     * @return string
     */
    protected function parseUri(string $uri): string
    {
        return preg_replace('@\/\{([\w]+?)(\?)?\}@', '\/?(?P<$1>[\w-]+)$2', $uri);
    }
}
