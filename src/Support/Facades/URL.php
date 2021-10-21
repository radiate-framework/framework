<?php

namespace Radiate\Support\Facades;

/**
 * @method static string current() Get the current URL without query parameters.
 * @method static string full() Get the current URL including query parameters.
 * @method static string previous(string|null $fallback = null) Get the URL for the previous request.
 * @method static string register(string $redirect = '') Return the registration URL
 * @method static string login(string $redirect = '/') Return the login URL
 * @method static string logout(string $redirect = '/') Return the logout URL
 * @method static string privacyPolicy() Return the privacy policy page if it is published.
 * @method static string archive(string $postType = 'post') Get the archive link
 * @method static string home(string $path = '', array $parameters = []) Return the home URL
 * @method static string to(string $path, array $parameters = []) Return the URL to the path specified
 * @method static void redirect(string $url, int $status = 302) Redirect to another page, with an optional status code
 * @method static string admin(string $path = '', array $parameters = []) Return the admin URL
 * @method static string ajax(string $action = '', array $parameters = []) Return the ajax URL
 * @method static string rest(string $path = '', array $parameters = []) Return the REST URL
 * @method static string route(string $name, array $parameters = []) Get a named route URL
 * @method static bool isValidUrl(string $path) Determine if the given path is a valid URL.
 * @method static string formatParameters(array $parameters = []) Return a formatted query string
 * @method static string asset(string $path) Generate the URL to an application asset.
 * @method static bool hasValidSignature(\Radiate\Http\Request $request) Determine if the request URL has a valid signature
 * @method static bool hasCorrectSignature(\Radiate\Http\Request $request) Determine if the request URL has a correct signature
 * @method static bool signatureHasNotExpired(\Radiate\Http\Request $request) Determine if the request URL is expired
 * @method static string signedUrl(string $path, array $parameters = [], int|null $expiration = null) Create a signed URL
 * @method static string temporarySignedUrl(string $path, int $expiration, array $parameters = []) Get a temporary signed URL
 * @method static \Radiate\Routing\UrlGenerator setKeyResolver(callable $keyResolver) Set the encryption key resolver.
 *
 * @see \Radiate\Routing\UrlGenerator
 */
class URL extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'url';
    }
}
