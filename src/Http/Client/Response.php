<?php

namespace Radiate\Http\Client;

use ArrayAccess;
use LogicException;
use Radiate\Support\Collection;
use Stringable;
use WP_HTTP_Requests_Response;

class Response implements ArrayAccess, Stringable
{
    /**
     * The underlying WordPress response.
     *
     * @var \WP_HTTP_Requests_Response
     */
    protected $response;

    /**
     * The decoded JSON response.
     *
     * @var array
     */
    protected $decoded;

    /**
     * Create a new response instance.
     *
     * @param  \WP_HTTP_Requests_Response  $response
     * @return void
     */
    public function __construct(WP_HTTP_Requests_Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->response->get_data();
    }

    /**
     * Get the JSON decoded body of the response as an array.
     *
     * @param  string|null $key
     * @param  mixed|null $default
     * @return array|mixed
     */
    public function json(?string $key = null, $default = null)
    {
        if (!$this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        if (!$key) {
            return $this->decoded;
        }

        return $this->decoded->$key ?? $default;
    }

    /**
     * Get the JSON decoded body of the response as a collection.
     *
     * @param  string|null  $key
     * @return \Radiate\Support\Collection
     */
    public function collect(?string $key = null): Collection
    {
        return Collection::make($this->json($key));
    }

    /**
     * Get the JSON decoded body of the response as an object.
     *
     * @return mixed
     */
    public function object()
    {
        return json_decode($this->body(), false);
    }

    /**
     * Get a header from the response.
     *
     * @param  string  $header
     * @return string
     */
    public function header(string $header): string
    {
        return $this->headers()[$header] ?? null;
    }

    /**
     * Get the headers from the response.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->response->get_headers()->getAll();
    }

    /**
     * Get the status code of the response.
     *
     * @return int
     */
    public function status(): int
    {
        return $this->response->get_status();
    }

    /**
     * Get the status message of the response.
     *
     * @return string
     */
    public function statusMessage(): string
    {
        return get_status_header_desc($this->status());
    }

    /**
     * Determine if the request was successful.
     *
     * @return bool
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     *
     * @return bool
     */
    public function ok(): bool
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response was a redirect.
     *
     * @return bool
     */
    public function redirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * Determine if the response indicates a client error occurred.
     *
     * @return bool
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     *
     * @return bool
     */
    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    /**
     * Get the response cookies.
     *
     * @return array
     */
    public function cookies(): array
    {
        return $this->response->get_cookies();
    }

    /**
     * Get the underlying PSR response for the response.
     *
     * @return \WP_HTTP_Requests_Response
     */
    public function toWordPressResponse(): WP_HTTP_Requests_Response
    {
        return $this->response;
    }

    /**
     * Throw an exception if a server or client error occurred.
     *
     * @return $this
     *
     * @throws \Radiate\Http\Client\RequestException
     */
    public function throw()
    {
        if ($this->serverError() || $this->clientError()) {
            throw new RequestException($this);
        }

        return $this;
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->json()[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->json()[$offset];
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetUnset($offset): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->body();
    }

    /**
     * Dynamically proxy other methods to the underlying response.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->response->{$method}(...$parameters);
    }
}
