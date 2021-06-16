<?php

namespace Radiate\Http;

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Radiate\Support\Str;

class Request implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use Macroable;

    /**
     * The query attributes
     *
     * @var array
     */
    protected $query;

    /**
     * The request attributes
     *
     * @var array
     */
    protected $request;

    /**
     * The cookie attributes
     *
     * @var array
     */
    protected $cookies;

    /**
     * The file attributes
     *
     * @var array
     */
    protected $files;

    /**
     * The server attributes
     *
     * @var array
     */
    protected $server;

    /**
     * The headers attributes
     *
     * @var array
     */
    protected $headers;

    /**
     * The request content
     *
     * @var array
     */
    protected $content;

    /**
     * The request json
     *
     * @var array
     */
    protected $json;

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
        $this->query = $query;
        $this->request = $request;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->headers = $this->getHeaders($server);
    }

    /**
     * Capture the global request
     *
     * @return \Radiate\Http\Request
     */
    public static function capture()
    {
        $request = new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);

        if (
            Str::startsWith($request->header('CONTENT_TYPE', ''), 'application/x-www-form-urlencoded') &&
            in_array($request->realMethod(), ['PUT', 'PATCH', 'DELETE'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = $data;
        }

        $request->request = $request->getInputSource();

        return $request;
    }

    /**
     * Create a new request from this one
     *
     * @param self $from
     * @param \Radiate\Http\Request|null $to
     * @return \Radiate\Http\Request
     */
    public static function createFrom(self $from, ?Request $to = null)
    {
        $request = $to ?: new static;

        $request->query = $from->query;
        $request->request = $from->request;
        $request->cookies = $from->cookies;
        $request->files = $from->files;
        $request->server = $from->server;
        $request->headers = $from->headers;
        $request->content = $from->content;
        $request->json = $from->json;

        $request->setUserResolver($from->getUserResolver());
        $request->setRouteResolver($from->getRouteResolver());

        return $request;
    }

    /**
     * Normalize the header key
     *
     * @param string $key
     * @return string
     */
    protected function normalizeHeaderKeys(string $key): string
    {
        return Str::of($key)->lower->replace('_', '-');
    }

    /**
     * Get the headers from the server global
     *
     * @param array $server
     * @return array
     */
    protected function getHeaders(array $server): array
    {
        $headers = [];

        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[$this->normalizeHeaderKeys(substr($key, 5))] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'])) {
                $headers[$this->normalizeHeaderKeys($key)] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get a server attribute
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Get a header
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function header(string $key, $default = null)
    {
        return $this->headers[$this->normalizeHeaderKeys($key)] ?? $default;
    }

    /**
     * Get the query attributes
     *
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function query(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->query[$key] ?? $default;
        }

        return $this->query;
    }

    /**
     * Get the request attributes
     *
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function post(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->request[$key] ?? $default;
        }

        return $this->request;
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
        return strtoupper($this->server('REQUEST_METHOD', 'GET'));
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
        return $this->header('X_REQUESTED_WITH') == 'XMLHttpRequest';
    }

    /**
     * Determine if the request can accept a JSON response
     *
     * @return bool
     */
    public function wantsJson()
    {
        return strpos($this->header('ACCEPT', '*/*'), '/json') !== false;
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return Str::contains($this->header('CONTENT_TYPE'), ['/json', '+json']);
    }

    /**
     * Determine if the request expects a JSON response
     *
     * @return bool
     */
    public function expectsJson()
    {
        return $this->ajax() || $this->wantsJson();
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
        if (!$this->json) {
            $this->json = json_decode($this->getContent(), true);
        }

        if (is_null($key)) {
            return $this->json;
        }

        return $this->json[$key] ?? $default;
    }

    /**
     * Returns the request body content.
     *
     * @return string
     */
    public function getContent()
    {
        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * Get the input source for the request.
     *
     * @return array
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return in_array($this->realMethod(), ['GET', 'HEAD'])
            ? $this->query
            : $this->request;
    }

    /**
     * Retrieve input as a boolean value.
     *
     * Returns true when value is "1", "true", "on", and "yes". Otherwise, returns false.
     *
     * @param  string|null  $key
     * @param  bool  $default
     * @return bool
     */
    public function boolean(?string $key = null, bool $default = false)
    {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
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
     * Get the bearer token from the request headers.
     *
     * @return string|null
     */
    public function bearerToken()
    {
        $header = $this->header('Authorization', '');

        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }
    }

    /**
     * Determine if the attribute exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return isset(
            array_merge($this->getInputSource(), $this->route()->parameters())[$key]
        );
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
        return $this->getInputSource()[$key] ?? $default;
    }

    /**
     * Add an attribute to the request
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add(string $key, $value)
    {
        return in_array($this->realMethod(), ['GET', 'HEAD'])
            ? $this->query[$key] = $value
            : $this->request[$key] = $value;
    }

    /**
     * Remove the attribute from the request
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key)
    {
        if (in_array($this->realMethod(), ['GET', 'HEAD'])) {
            unset($this->query[$key]);
        } else {
            unset($this->request[$key]);
        }
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
    public function setUserResolver(Closure $resolver)
    {
        $this->userResolver = $resolver;

        return $this;
    }

    /**
     * Get the user resolver
     *
     * @return \Closure
     */
    public function getUserResolver()
    {
        return $this->userResolver ?: function () {
            //
        };
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
     * Determine if an instance exists
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get an instance
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set an instance
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->add($key, $value);
    }

    /**
     * Unset any instances or bindings
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * Return the object as an array
     *
     * @return array
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * Return the object as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->request, $this->query);
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
    public function __isset(string $key)
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
    public function __set(string $key, $value)
    {
        $this->add($key, $value);
    }

    /**
     * Remove an attribute from the request
     *
     * @param string $key
     * @return void
     */
    public function __unset(string $key)
    {
        $this->remove($key);
    }
}
