<?php

namespace Radiate\Support\Facades;

/**
 * @method static mixed server(string $key, mixed $default = null) Get a server attribute
 * @method static mixed header(string $key, mixed $default = null) Get a header
 * @method static bool isMethod(string $method) Determine if the method matches the given method
 * @method static string method() Get the intended method
 * @method static string realMethod() Get the real request method
 * @method static \Radiate\Http\Request merge(array $attributes) Merge the attributes into the request
 * @method static bool ajax() Determine if the request was made with AJAX
 * @method static bool wantsJson() Determine if the request can accept a JSON response
 * @method static bool expectsJson() Determine if the request expects a JSON response
 * @method static bool has(string $key) Determine if the attribute exists
 * @method static mixed get(string $key, mixed $default = null) Get an attribute or fallback
 * @method static void add(string $key, mixed $value) Add an attribute to the request
 * @method static void remove(string $key) Remove the attribute from the request
 * @method static mixed user() Get the request user
 * @method static \Radiate\Http\Request setUserResolver(\Closure $resolver) Set the user resolver
 * @method static \Closure getUserResolver() Get the user resolver
 * @method static array all() Return the object as an array
 * @method static array toArray() Return the object as an array
 * @method static string toJson() Return the object as a json string
 *
 * @see \Radiate\Http\Request
 */
class Request extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'request';
    }
}
