<?php

namespace Radiate\Support\Facades;

/**
 * @method static string current() Get the current URL without query parameters.
 * @method static string full() Get the current URL including query parameters.
 * @method static string previous(?string $fallback = null) Get the URL for the previous request.
 * @method static string register(string $redirect = '') Return the registration URL
 * @method static string login(string $redirect = '/') Return the login URL
 * @method static string logout(string $redirect = '/') Return the logout URL
 * @method static string home(string $path = '') Return the home URL
 * @method static string to(string $path) Return the URL to the path specified
 * @method static void redirect(string $url, int $status = 302) Redirect to another page, with an optional status code
 * @method static string admin(string $path = '') Return the admin URL
 * @method static string ajax(string $action = '') Return the ajax URL
 * @method static string rest(string $path = '') Return the REST URL
 * @method static bool isValidUrl(string $path) Determine if the given path is a valid URL.
 * @method static string asset(string $path) Generate the URL to an application asset.
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
