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
     * @return \Closure
     */
    public function handle(Request $request)
    {
        return function (WP_REST_Request $wpRequest) use ($request) {
            $request->setRouteResolver(function () {
                return $this;
            });

            die($this->runRequestThroughStack(
                $request,
                $this->parameters = $wpRequest->get_url_params()
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

    /**
     * Generate a URL for the route
     *
     * @param \Radiate\Routing\UrlGenerator $url
     * @param array $parameters
     * @return string
     */
    public function generateUrl(UrlGenerator $url, array $parameters = [])
    {
        $path = preg_replace_callback('/\{(.*?)\}/', function ($matches) use (&$parameters) {
            if (isset($parameters[$matches[1]]) && $value = $parameters[$matches[1]]) {
                unset($parameters[$matches[1]]);

                return $value;
            }

            return $matches[0];
        }, $this->uri());

        return $url->rest($path, $parameters);
    }
}
