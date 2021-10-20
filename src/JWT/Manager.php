<?php

namespace Radiate\JWT;

use Firebase\JWT\JWT;
use Radiate\Auth\AuthenticationException;
use Radiate\Foundation\Application;
use Radiate\Http\Request;
use UnexpectedValueException;

class Manager
{
    /**
     * The application instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * The JWT service
     *
     * @var \Firebase\JWT\JWT
     */
    protected $jwt;

    /**
     * The JWT signing key
     *
     * @var string
     */
    protected $key;

    /**
     * The JWT algorithm
     *
     * @var string
     */
    protected $algo = 'HS256';

    /**
     * The JWT claims
     *
     * @var array
     */
    protected $claims = [];

    /**
     * Create the JWT manager instance
     *
     * @param \Radiate\Foundation\Application $app
     * @param \Firebase\JWT\JWT $jwt
     */
    public function __construct(Application $app, JWT $jwt)
    {
        $this->app = $app;
        $this->jwt = $jwt;
        $this->key = $app['config']['app.key'];
    }

    /**
     * Converts and signs a PHP object or array into a JWT string.
     *
     * @param mixed $payload
     * @return string
     */
    public function encode($payload): string
    {
        return $this->jwt->encode($payload, $this->key, $this->algo);
    }

    /**
     * Decodes a JWT string into a Payload instance.
     *
     * @param string $jwt
     * @return \Radiate\JWT\Payload
     *
     * @throws \Radiate\Auth\AuthenticationException
     */
    public function decode(string $jwt): Payload
    {
        try {
            return new Payload(
                (array) $this->jwt->decode($jwt, $this->key, [$this->algo])
            );
        } catch (UnexpectedValueException $e) {
            throw new AuthenticationException($e->getMessage());
        }
    }

    /**
     * Dynamically call the JWT builder
     *
     * @param string $method
     * @param array $parameters
     * @return \Radiate\JWT\Builder
     */
    public function __call(string $method, array $parameters = [])
    {
        return (new Builder($this))->$method(...$parameters);
    }

    /**
     * Get the request bearer token
     *
     * @return string|null
     */
    /**
     * Get the JWT from the request
     *
     * @param \Radiate\Http\Request $request
     * @return string|null
     */
    public function token(Request $request): ?string
    {
        return $request->bearerToken();
    }

    /**
     * Get the JWT payload from the request
     *
     * @param \Radiate\Http\Request $request
     * @return \Radiate\JWT\Payload|null
     */
    public function payload(Request $request): ?Payload
    {
        if ($token = $this->token($request)) {
            return $this->decode($token);
        }

        return null;
    }
}
