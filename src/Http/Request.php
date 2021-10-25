<?php

namespace Radiate\Http;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Radiate\Http\Concerns\InteractsWithContentTypes;
use Radiate\Http\Concerns\InteractsWithInput;
use Radiate\Support\Str;
use Stringable;
use WP_REST_Request;

class Request extends WP_REST_Request implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    use InteractsWithContentTypes, InteractsWithInput, Macroable;

    /**
     * The cookie attributes
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * The server attributes
     *
     * @var array
     */
    protected $server = [];

    /**
     * The user resolver
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * The route resolver
     *
     * @var \Closure
     */
    protected $routeResolver;

    /**
     * Create the request instance
     *
     * @param array $query
     * @param array $request
     * @param array $cookies
     * @param array $files
     * @param array $server
     */
    public function __construct(array $query = [], array $request = [], array $cookies = [], array $files = [], array $server = [])
    {
        parent::__construct($server['REQUEST_METHOD'], $server['PATH_INFO'] ?? '/');
        $this->set_query_params(wp_unslash($query));
        $this->set_body_params(wp_unslash($request));
        $this->set_file_params($files);
        $this->set_headers($this->getHeadersFromServer(wp_unslash($server)));
        $this->set_body(file_get_contents('php://input'));

        $this->set_cookie_params($cookies);
        $this->set_server_params($server);
    }

    /**
     * Capture the global request
     *
     * @return \Radiate\Http\Request
     */
    public static function capture()
    {
        return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * Create a new request from this one
     *
     * @param \Radiate\Http\Request $from
     * @param \Radiate\Http\Request|null $to
     * @return static
     */
    public static function createFrom(self $from, ?Request $to = null)
    {
        $request = $to ?: new static;

        $request->set_method($from->get_method());
        $request->set_route($from->get_route());
        $request->set_query_params($from->get_query_params());
        $request->set_body_params($from->get_body_params());
        $request->set_file_params($from->get_file_params());
        $request->set_headers($from->get_headers());
        $request->set_body($from->get_body());
        $request->set_default_params($from->get_default_params());
        $request->set_attributes($from->get_attributes());
        $request->set_url_params($from->get_url_params());
        $request->set_cookie_params($from->get_cookie_params());
        $request->set_server_params($from->get_server_params());
        $request->setUserResolver($from->getUserResolver());
        $request->setRouteResolver($from->getRouteResolver());

        return $request;
    }

    /**
     * Create a Radiate request from a WP_REST_Request instance.
     *
     * @param  \WP_REST_Request  $request
     * @return static
     */
    public static function createFromBase(WP_REST_Request $request)
    {
        $newRequest = new static;

        $newRequest->set_method($request->get_method());
        $newRequest->set_route($request->get_route());
        $newRequest->set_query_params($request->get_query_params());
        $newRequest->set_body_params($request->get_body_params());
        $newRequest->set_file_params($request->get_file_params());
        $newRequest->set_headers($request->get_headers());
        $newRequest->set_body($request->get_body());
        $newRequest->set_default_params($request->get_default_params());
        $newRequest->set_attributes($request->get_attributes());
        $newRequest->set_url_params($request->get_url_params());
        $newRequest->set_cookie_params($_COOKIE);
        $newRequest->set_server_params($_SERVER);

        return $newRequest;
    }



    /**
     * Get the headers from the server global
     *
     * @param array $server
     * @return array
     */
    protected function getHeadersFromServer(array $server): array
    {
        $headers = [];

        foreach ($server as $key => $value) {
            if (Str::startsWith($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif ($key === 'REDIRECT_HTTP_AUTHORIZATION' && empty($server['HTTP_AUTHORIZATION'])) {
                /*
				 * In some server configurations, the authorization header is passed in this alternate location.
				 * Since it would not be passed in in both places we do not check for both headers and resolve.
				 */
                $headers['AUTHORIZATION'] = $value;
            } elseif (in_array($key, ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE', 'PHP_AUTH_USER', 'PHP_AUTH_PW'])) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * Set the server parameters
     *
     * @param array $server
     * @return void
     */
    public function set_server_params(array $server = []): void
    {
        $this->server = $server;
    }

    /**
     * Get the server parameters
     *
     * @return array
     */
    public function get_server_params(): array
    {
        return $this->server;
    }

    /**
     * Set the cookie parameters
     *
     * @param array $cookies
     * @return void
     */
    public function set_cookie_params(array $cookies = []): void
    {
        $this->cookies = $cookies;
    }

    /**
     * Get the cookie parameters
     *
     * @return array
     */
    public function get_cookie_params(): array
    {
        return $this->cookies;
    }

    /**
     * Get the request route
     *
     * @param string|null $parameters
     * @param mixed|null $default
     * @return \Radiate\Routing\Route|mixed
     */
    public function route(?string $parameter = null, $default = null)
    {
        $route = call_user_func($this->getRouteResolver());

        if (is_null($route) || is_null($parameter)) {
            return $route;
        }

        return $route->parameter($parameter, $default);
    }

    /**
     * Set the route resolver
     *
     * @param \Closure $resolver
     * @return self
     */
    public function setRouteResolver(Closure $resolver)
    {
        $this->routeResolver = $resolver;

        return $this;
    }

    /**
     * Get the route resolver
     *
     * @return \Closure
     */
    public function getRouteResolver()
    {
        return $this->routeResolver ?: function () {
            //
        };
    }

    /**
     * Determine if the method matches the given method
     *
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) == $this->method();
    }

    /**
     * Get the intended method
     *
     * @return string
     */
    public function method()
    {
        return strtoupper($this->get('_method', $this->realMethod()));
    }

    /**
     * Get the real request method
     *
     * @return string
     */
    public function realMethod()
    {
        return $this->get_method();
    }

    /**
     * Merge the attributes into the request
     *
     * @param array $attributes
     * @return self
     */
    public function merge(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->add($key, $value);
        }

        return $this;
    }

    /**
     * Determine if the request was made with AJAX
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->header('x-requested-with') == 'XMLHttpRequest';
    }

    /**
     * Get the JSON payload for the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function json(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_json_params()[$key] ?? $default;
        }

        return $this->get_json_params();
    }

    /**
     * Returns the request body content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->get_body();
    }

    /**
     * Get the client user agent.
     *
     * @return string|null
     */
    public function userAgent(): ?string
    {
        return $this->header('user-agent');
    }

    /**
     * Determine if the request is secure
     *
     * @return boolean
     */
    public function isSecure(): bool
    {
        $https = $this->server('HTTPS');

        return $https && strtolower($https) !== 'off';
    }

    /**
     * Get the request scheme
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * Get the port
     *
     * @return integer
     */
    public function getPort(): int
    {
        if (!$this->header('host')) {
            return $this->server('SERVER_PORT');
        }

        return $this->isSecure() ? 443 : 80;
    }

    /**
     * Get the host including port if non-standard
     *
     * @return string
     */
    public function getHttpHost(): string
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();

        if (($scheme == 'http' && $port == 80) || ($scheme == 'https' && $port == 443)) {
            return $this->getHost();
        }

        return $this->getHost() . ':' . $port;
    }

    /**
     * Get the host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->header('host', $this->server('SERVER_NAME', $this->server('SERVER_ADDR')));
    }

    /**
     * Get the scheme and host
     *
     * @return string
     */
    public function getSchemeAndHttpHost(): string
    {
        return $this->getScheme() . '://' . $this->getHttpHost();
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->server('QUERY_STRING', '');
    }

    /**
     * Get the base path
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return strtok($this->server('REQUEST_URI'), '?');
    }

    /**
     * Get the URL
     *
     * @return string
     */
    public function url(): string
    {
        return $this->getSchemeAndHttpHost() . $this->getBaseUrl();
    }

    /**
     * Get the full URL including query parameters
     *
     * @return string
     */
    public function fullUrl(): string
    {
        $qs = $this->getQueryString();

        return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . ($qs ? '?' . $qs : '');
    }

    /**
     * Returns the user.
     *
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->server('PHP_AUTH_USER');
    }

    /**
     * Returns the password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->server('PHP_AUTH_PW');
    }

    /**
     * Get an attribute or fallback
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->input($key) ?? $default;
    }

    /**
     * Add an attribute to the request
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add(string $key, $value): void
    {
        $this->params['defaults'][$key] = $value;
    }

    /**
     * Remove the attribute from the request
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this[$key]);
    }

    /**
     * Get the request user
     *
     * @return mixed
     */
    public function user()
    {
        return call_user_func($this->getUserResolver());
    }

    /**
     * Set the user resolver
     *
     * @param \Closure $resolver
     * @return self
     */
    public function setUserResolver(Closure $resolver): self
    {
        $this->userResolver = $resolver;

        return $this;
    }

    /**
     * Get the user resolver
     *
     * @return \Closure
     */
    public function getUserResolver(): Closure
    {
        return $this->userResolver ?: function () {
            //
        };
    }

    /**
     * Return the object as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * Return the object as a json string
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->all(), $options);
    }

    /**
     * Return the request to be encoded as json
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * Return the request as a json string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Determine if the attribute exists
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * Get an attribute or fallback
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Add an attribute to the request
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->add($key, $value);
    }

    /**
     * Remove an attribute from the request
     *
     * @param string $key
     * @return void
     */
    public function __unset(string $key): void
    {
        $this->remove($key);
    }
}
