<?php

namespace Radiate\Support\Facades;

use Radiate\Http\Client\Factory;

/**
 * @method static array getPromise(string $url) Retrieve the pending request promise.
 * @method static \Radiate\Http\Client\PendingRequest async(bool $async = true) Toggle asynchronicity in requests.
 * @method static \Radiate\Http\Client\PendingRequest baseUrl(string $url) Set the base URL for the pending request.
 * @method static \Radiate\Http\Client\PendingRequest asJson() Indicate the request contains JSON.
 * @method static \Radiate\Http\Client\PendingRequest asForm() Indicate the request contains form parameters.
 * @method static \Radiate\Http\Client\PendingRequest bodyFormat(string $format) Specify the body format of the request.
 * @method static \Radiate\Http\Client\PendingRequest contentType(string $contentType) Specify the request's content type.
 * @method static \Radiate\Http\Client\PendingRequest acceptJson() Indicate that JSON should be returned by the server.
 * @method static \Radiate\Http\Client\PendingRequest accept(string $contentType) Indicate the type of content that should be returned by the server.
 * @method static \Radiate\Http\Client\PendingRequest withHeaders(array $headers) Add the given headers to the request.
 * @method static \Radiate\Http\Client\PendingRequest withBasicAuth(string $username, string $password) Specify the basic authentication username and password for the request.
 * @method static \Radiate\Http\Client\PendingRequest withToken(string $token, string $type = 'Bearer') Specify an authorization token for the request.
 * @method static \Radiate\Http\Client\PendingRequest withCookies(array $cookies, string $domain) Specify the cookies that should be included with the request.
 * @method static \Radiate\Http\Client\PendingRequest withoutRedirecting() Indicate that redirects should not be followed.
 * @method static \Radiate\Http\Client\PendingRequest withoutVerifying() Indicate that TLS certificates should not be verified.
 * @method static \Radiate\Http\Client\PendingRequest timeout(int $seconds) Specify the timeout (in seconds) for the request.
 * @method static \Radiate\Http\Client\PendingRequest withOptions(array $options) Merge new options into the client.
 * @method static \Radiate\Http\Client\Response get(string $url, array $query = []) Issue a GET request to the given URL.
 * @method static \Radiate\Http\Client\Response post(string $url, array $data = []) Issue a POST request to the given URL.
 * @method static \Radiate\Http\Client\Response put(string $url, array $data = []) Issue a PUT request to the given URL.
 * @method static \Radiate\Http\Client\Response patch(string $url, array $data = []) Issue a PATCH request to the given URL.
 * @method static \Radiate\Http\Client\Response delete(string $url, array $data = []) Issue a DELETE request to the given URL.
 * @method static \Radiate\Http\Client\Response send(string $method, string $url, array $options = []) Send the request to the given URL.
 * @method static array pool(callable $callback) Send a pool of asynchronous requests concurrently.
 *
 * @see \Radiate\Http\Client\Factory|\Radiate\Http\Client\PendingRequest
 */
class Http extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
