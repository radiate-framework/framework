<?php

namespace Radiate\Support\Facades;

use Radiate\Http\Client\Factory;

/**
 * @method static \Radiate\Http\Client\PendingRequest asJson()
 * @method static \Radiate\Http\Client\PendingRequest asForm()
 * @method static \Radiate\Http\Client\PendingRequest bodyFormat(string $format)
 * @method static \Radiate\Http\Client\PendingRequest contentType(string $contentType)
 * @method static \Radiate\Http\Client\PendingRequest acceptJson()
 * @method static \Radiate\Http\Client\PendingRequest accept(string $contentType)
 * @method static \Radiate\Http\Client\PendingRequest withHeaders(array $headers)
 * @method static \Radiate\Http\Client\PendingRequest withBasicAuth(string $username, string $password)
 * @method static \Radiate\Http\Client\PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method static \Radiate\Http\Client\PendingRequest withCookies(array $cookies, string $domain)
 * @method static \Radiate\Http\Client\PendingRequest withoutRedirecting()
 * @method static \Radiate\Http\Client\PendingRequest withoutVerifying()
 * @method static \Radiate\Http\Client\PendingRequest timeout(int $seconds)
 * @method static \Radiate\Http\Client\PendingRequest withOptions(array $options)
 * @method static \Radiate\Http\Client\Response get(string $url, array $query = [])
 * @method static \Radiate\Http\Client\Response post(string $url, array $data = [])
 * @method static \Radiate\Http\Client\Response put(string $url, array $data = [])
 * @method static \Radiate\Http\Client\Response patch(string $url, array $data = [])
 * @method static \Radiate\Http\Client\Response delete(string $url, array $data = [])
 * @method static \Radiate\Http\Client\Response send(string $method, string $url, array $options = [])
 * @method static array pool(callable $callback)
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
