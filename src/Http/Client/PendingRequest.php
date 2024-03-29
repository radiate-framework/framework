<?php

namespace Radiate\Http\Client;

use Closure;
use Requests;
use Requests_Exception;
use Requests_Response;
use WP_HTTP_Requests_Response;

class PendingRequest
{
    /**
     * The factory instance.
     *
     * @var \Radiate\Http\Client\Factory|null
     */
    protected $factory;

    /**
     * The base URL for the request.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * The body format
     *
     * @var string
     */
    protected $bodyFormat = '';

    /**
     * The request cookies.
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * The request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Whether the requests should be asynchronous.
     *
     * @var bool
     */
    protected $async = false;

    /**
     * The pending request promise.
     *
     * @var array
     */
    protected $promise;

    /**
     * Create a new HTTP Client instance.
     *
     * @param  \Radiate\Http\Client\Factory|null  $factory
     * @return void
     */
    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory;
    }

    /**
     * Toggle asynchronicity in requests.
     *
     * @param  bool  $async
     * @return static
     */
    public function async(bool $async = true)
    {
        $this->async = $async;

        return $this;
    }

    /**
     * Retrieve the pending request promise.
     *
     * @return array
     */
    public function getPromise()
    {
        return $this->promise;
    }

    /**
     * Set the base URL for the pending request.
     *
     * @param  string  $url
     * @return $this
     */
    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Specify the body format of the request.
     *
     * @param  string  $format
     * @return $this
     */
    public function bodyFormat(string $format)
    {
        $this->bodyFormat = $format;

        return $this;
    }

    /**
     * Indicate the request contains JSON.
     *
     * @return $this
     */
    public function asJson()
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    /**
     * Indicate the request contains form parameters.
     *
     * @return $this
     */
    public function asForm()
    {
        return $this->bodyFormat('form')->contentType('application/x-www-form-urlencoded');
    }

    /**
     * Specify the request's content type.
     *
     * @param  string  $contentType
     * @return $this
     */
    public function contentType(string $contentType)
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    /**
     * Indicate that JSON should be returned by the server.
     *
     * @return $this
     */
    public function acceptJson()
    {
        return $this->accept('application/json');
    }

    /**
     * Indicate the type of content that should be returned by the server.
     *
     * @param  string  $contentType
     * @return $this
     */
    public function accept(string $contentType)
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /**
     * Add the given headers to the request.
     *
     * @param  array  $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->options = array_merge_recursive($this->options, [
            'headers' => $headers,
        ]);

        return $this;
    }

    /**
     * Specify the basic authentication username and password for the request.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withBasicAuth(string $username, string $password)
    {
        return $this->withHeaders(
            ['Authorization' => 'Basic ' . base64_encode($username . ':' . $password)]
        );
    }

    /**
     * Specify an authorization token for the request.
     *
     * @param  string  $token
     * @param  string  $type
     * @return $this
     */
    public function withToken(string $token, string $type = 'Bearer')
    {
        return $this->withHeaders(
            ['Authorization' => trim($type . ' ' . $token)]
        );
    }

    /**
     * Specify the cookies that should be included with the request.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function withCookies(array $cookies)
    {
        $this->options = array_merge_recursive($this->options, [
            'cookies' => $cookies,
        ]);

        return $this;
    }

    /**
     * Indicate that redirects should not be followed.
     *
     * @return $this
     */
    public function withoutRedirecting()
    {
        $this->options['redirection'] = 0;

        return $this;
    }

    /**
     * Indicate that TLS certificates should not be verified.
     *
     * @return $this
     */
    public function withoutVerifying()
    {
        $this->options['sslverify'] = false;
        $this->options['verify'] = false;

        return $this;
    }

    /**
     * Specify the timeout (in seconds) for the request.
     *
     * @param  int  $seconds
     * @return $this
     */
    public function timeout(int $seconds)
    {
        $this->options['timeout'] = $seconds;

        return $this;
    }

    /**
     * Merge new options into the client.
     *
     * @param  array  $options
     * @return $this
     */
    public function withOptions(array $options)
    {
        $this->options = array_merge_recursive($this->options, $options);

        return $this;
    }

    /**
     * Issue a GET request to the given URL.
     *
     * @param  string  $url
     * @param  array|string|null  $query
     * @return \Radiate\Http\Client\Response
     */
    public function get(string $url, $query = null)
    {
        return $this->send('GET', $url, ['body' => $query]);
    }

    /**
     * Issue a POST request to the given URL.
     *
     * @param  string  $url
     * @param  string|array  $data
     * @return \Radiate\Http\Client\Response
     */
    public function post(string $url, $data = [])
    {
        return $this->send('POST', $url, ['body' => $data]);
    }

    /**
     * Issue a PATCH request to the given URL.
     *
     * @param  string  $url
     * @param  string|array  $data
     * @return \Radiate\Http\Client\Response
     */
    public function patch(string $url, $data = [])
    {
        return $this->send('PATCH', $url, ['body' => $data]);
    }

    /**
     * Issue a PUT request to the given URL.
     *
     * @param  string  $url
     * @param  string|array  $data
     * @return \Radiate\Http\Client\Response
     */
    public function put(string $url, $data = [])
    {
        return $this->send('PUT', $url, ['body' => $data]);
    }

    /**
     * Issue a DELETE request to the given URL.
     *
     * @param  string  $url
     * @param  string|array  $data
     * @return \Radiate\Http\Client\Response
     */
    public function delete(string $url, $data = [])
    {
        return $this->send('DELETE', $url, ['body' => $data]);
    }

    /**
     * Send the request to the given URL.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return \Radiate\Http\Client\Response|static
     *
     * @throws \Radiate\Http\Client\ConnectionException
     */
    public function send(string $method, string $url, array $options = [])
    {
        $url = ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');

        if ($this->bodyFormat === 'json' && $options['body']) {
            $options['body'] = json_encode($options['body']);
        }

        if ($this->async) {
            return $this->makePromise($method, $url, $options);
        }

        return $this->sendRequest($method, $url, $options);
    }

    /**
     * Store the request as a promise
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return static
     */
    protected function makePromise(string $method, string $url, array $options = [])
    {
        $options = $this->mergeOptions($options);

        $this->promise = [
            'url'     => $url,
            'headers' => $options['headers'],
            'data'    => $options['body'],
            'type'    => $method,
            'options' => $options,
            'cookies' => $options['cookies'],
        ];

        return $this;
    }

    /**
     * Send the request to the given URL.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return \Radiate\Http\Client\Response
     *
     * @throws \Radiate\Http\Client\ConnectionException
     */
    protected function sendRequest(string $method, string $url, array $options = [])
    {
        $response = wp_remote_request($url, $this->mergeOptions([
            'method' => $method,
        ], $options));

        if (!is_wp_error($response)) {
            return new Response($response['http_response']);
        }

        throw new ConnectionException($response->get_error_message());
    }

    /**
     * Merge the given options with the current request options.
     *
     * @param  array  $options
     * @return array
     */
    protected function mergeOptions(...$options)
    {
        return array_merge_recursive($this->options, ...$options);
    }

    /**
     * Send a pool of asynchronous requests concurrently.
     *
     * @param  callable  $callback
     * @return array
     */
    public function pool(callable $callback)
    {
        $promises = [];

        $callback($pool = new Pool());

        foreach ($pool->getRequests() as $key => $item) {
            $promises[$key] = $item instanceof PendingRequest ? $item->getPromise() : $item;
        }

        $responses = array_map(
            [$this, 'resolveResponse'],
            Requests::request_multiple($promises)
        );

        ksort($responses);

        return $responses;
    }

    /**
     * Resolve the response
     *
     * @param \Requests_Response|\Requests_Exception $response
     * @return \Radiate\Http\Client\Response
     *
     * @throws \Radiate\Http\Client\ConnectionException
     */
    protected function resolveResponse($response)
    {
        if ($response instanceof Requests_Exception) {
            throw new ConnectionException($response->getMessage());
        }

        $response = new WP_HTTP_Requests_Response($response);

        return new Response($response);
    }
}
