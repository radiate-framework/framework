<?php

namespace Radiate\Http\Concerns;

use Radiate\Support\Str;

trait InteractsWithInput
{
    /**
     * Retrieve a server variable from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function server(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_server_params()[$key] ?? $default;
        }

        return $this->get_server_params();
    }

    /**
     * Determine if a header is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasHeader(string $key): bool
    {
        return !is_null($this->header($key));
    }

    /**
     * Retrieve a header from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function header(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_header($key) ?? $default;
        }

        return $this->get_headers();
    }

    /**
     * Get the bearer token from the request headers.
     *
     * @return string|null
     */
    public function bearerToken()
    {
        $header = $this->header('authorization', '');

        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->has($key);
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key): bool
    {
        $keys = (array) $key;

        $input = $this->all();

        foreach ($keys as $key) {
            if (!$input[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request contains any of the given inputs.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function hasAny($keys): bool
    {
        $keys = (array) $keys;

        foreach ($keys as $key) {
            if ($this->has_param($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply the callback if the request contains the given input item key.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @return $this|mixed
     */
    public function whenHas(string $key, callable $callback)
    {
        if ($this->has($key)) {
            return $callback($this->input($key)) ?: $this;
        }

        return $this;
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function filled($key): bool
    {
        $keys = (array) $key;

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request contains an empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function isNotFilled($key): bool
    {
        $keys = (array) $key;

        foreach ($keys as $value) {
            if (!$this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request contains a non-empty value for any of the given inputs.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function anyFilled($keys): bool
    {
        $keys = (array) $keys;

        foreach ($keys as $key) {
            if ($this->filled($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply the callback if the request contains a non-empty value for the given input item key.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @return $this|mixed
     */
    public function whenFilled(string $key, callable $callback)
    {
        if ($this->filled($key)) {
            return $callback($this->input($key)) ?: $this;
        }

        return $this;
    }

    /**
     * Determine if the request is missing a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function missing($key): bool
    {
        return !$this->has($key);
    }

    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyString(string $key): bool
    {
        $value = $this->input($key);

        return !is_bool($value) && !is_array($value) && trim((string) $value) === '';
    }

    /**
     * Get the keys for all of the input and files.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed|null  $keys
     * @return array
     */
    public function all($keys = null): array
    {
        $input = $this->get_params() + $this->get_file_params();

        if (!$keys) {
            return $input;
        }

        $results = [];

        foreach ((array) $keys as $key) {
            $results[$key] = $input[$key];
        }

        return $results;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function input(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_param($key) ?? $default;
        }

        return $this->get_params();
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
    public function boolean(?string $key = null, bool $default = false): bool
    {
        return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get a subset containing the provided keys with values from the input data.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function only($keys): array
    {
        return $this->all($keys);
    }

    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function except($keys): array
    {
        $results = $this->all();

        return array_diff_key($results, array_flip((array) $keys));
    }

    /**
     * Retrieve a query string item from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function query(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_query_params()[$key] ?? $default;
        }

        return $this->get_query_params();
    }

    /**
     * Retrieve a request payload item from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function post(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_body_params()[$key] ?? $default;
        }

        return $this->get_body_params();
    }

    /**
     * Determine if a cookie is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasCookie(string $key): bool
    {
        return !is_null($this->cookie($key));
    }

    /**
     * Retrieve a cookie from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function cookie(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->get_cookie_params()[$key] ?? $default;
        }

        return $this->get_cookie_params();
    }
}
