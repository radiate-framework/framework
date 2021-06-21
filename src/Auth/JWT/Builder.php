<?php

namespace Radiate\Auth\JWT;

use ArrayAccess;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use LogicException;

class Builder implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    /**
     * The JWT key
     *
     * @var string
     */
    protected $key;

    /**
     * The JWT claims
     *
     * @var array
     */
    protected $claims = [];

    /**
     * Create the builder instance
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Set the issuer of the JWT
     *
     * @param mixed $issuer
     * @return static
     */
    public function issuer($issuer)
    {
        $this->iss = $issuer;

        return $this;
    }

    /**
     * Set the subject of the JWT (the user)
     *
     * @param mixed $subject
     * @return static
     */
    public function subject($subject)
    {
        $this->sub = $subject;

        return $this;
    }

    /**
     * Set the recipient for which the JWT is intended
     *
     * @param mixed $audience
     * @return static
     */
    public function audience($audience)
    {
        $this->aud = $audience;

        return $this;
    }

    /**
     * Set the time after which the JWT expires
     *
     * @param int $time
     * @return static
     */
    public function expirationTime($time)
    {
        $this->exp = $time;

        return $this;
    }

    /**
     * Set the time before which the JWT must not be accepted for processing
     *
     * @param int $time
     * @return static
     */
    public function notBeforeTime($time)
    {
        $this->nbf = $time;

        return $this;
    }

    /**
     * Set the time at which the JWT was issued; can be used to determine age of
     * the JWT
     *
     * @param int $time
     * @return static
     */
    public function issuedAtTime($time)
    {
        $this->iat = $time;

        return $this;
    }

    /**
     * Set the unique identifier; can be used to prevent the JWT from being
     * replayed (allows a token to be used only once)
     *
     * @param int|string $id
     * @return static
     */
    public function id($id)
    {
        $this->jti = $id;

        return $this;
    }

    /**
     * Encode the JWT
     *
     * @return string
     */
    public function encode()
    {
        return JWT::encode($this->claims, $this->key);
    }

    /**
     * Decode the token
     *
     * @param string $token
     * @return \Radiate\Auth\JWT\Response
     */
    public function decode(string $token)
    {
        return new Response($token, $this->key);
    }

    /**
     * Dynamically set claims
     *
     * @param string $method
     * @param array $parameters
     * @return static
     */
    public function __call(string $method, array $parameters = [])
    {
        $this->$method = count($parameters) > 1 ? $parameters : $parameters[0];

        return $this;
    }

    /**
     * Dynamically get claims
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->claims[$key];
    }

    /**
     * Dynamically set claims
     *
     * @param string $key
     * @param mixed $claim
     * @return void
     */
    public function __set(string $key, $claim)
    {
        $this->claims[$key] = $claim;
    }

    /**
     * Get the instance as a string.
     *
     * @return array
     */
    public function __toString()
    {
        return $this->encode();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->claims;
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
        return isset($this->claims[$key]);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->claims[$key];
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
        $this->claims[$key] = $value;
    }

    /**
     * Unsets an offset.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->claims[$key]);
    }
}
