<?php

namespace Radiate\Auth\JWT;

use ArrayAccess;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use LogicException;

class Response implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    /**
     * The JWT claims
     *
     * @var object
     */
    protected $claims;

    /**
     * Create the response instance
     *
     * @param string $token
     * @param string $key
     * @return void
     */
    public function __construct(string $token, string $key)
    {
        $this->claims = JWT::decode($token, $key, ['HS256']);
    }

    /**
     * Issuer of the JWT
     *
     * @return mixed
     */
    public function issuer()
    {
        return $this->claims->iss;
    }

    /**
     * Subject of the JWT (the user)
     *
     * @return mixed
     */
    public function subject()
    {
        return $this->claims->sub;
    }

    /**
     * Recipient for which the JWT is intended
     *
     * @return mixed
     */
    public function audience()
    {
        return $this->claims->aud;
    }

    /**
     * Time after which the JWT expires
     *
     * @return int|null
     */
    public function expirationTime()
    {
        return $this->claims->exp;
    }

    /**
     * Time before which the JWT must not be accepted for processing
     *
     * @return int|null
     */
    public function notBeforeTime()
    {
        return $this->claims->nbf;
    }

    /**
     * Time at which the JWT was issued; can be used to determine age of the JWT
     *
     * @return int|null
     */
    public function issuedAtTime()
    {
        return $this->claims->iat;
    }

    /**
     * Unique identifier; can be used to prevent the JWT from being replayed
     * (allows a token to be used only once)
     *
     * @return int|string|null
     */
    public function id()
    {
        return $this->claims->jti;
    }

    /**
     * Dynamically retrieve claims
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->claims->$key;
    }

    /**
     * Get the instance as a string.
     *
     * @return array
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this->claims;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Whether or not an offset exists.
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->claims->$key);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->claims->$key;
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        throw new LogicException('JWT Response data may not be mutated using array access.');
    }

    /**
     * Unsets an offset.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        throw new LogicException('JWT Response data may not be mutated using array access.');
    }
}
