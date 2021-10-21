<?php

namespace Radiate\JWT;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Stringable;

class Payload implements Arrayable, ArrayAccess, Jsonable, JsonSerializable, Stringable
{
    /**
     * The payload
     *
     * @var array
     */
    protected $payload;

    /**
     * Create the payload instance
     *
     * @param array $payload
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * Get the issuer claim
     *
     * @return mixed|null
     */
    public function iss()
    {
        return $this->payload['iss'];
    }

    /**
     * Get the audience claim
     *
     * @return mixed|null
     */
    public function aud()
    {
        return $this->payload['aud'];
    }

    /**
     * Get the subject claim
     *
     * @return mixed|null
     */
    public function sub()
    {
        return $this->payload['sub'];
    }

    /**
     * Get the expiry claim
     *
     * @return mixed|null
     */
    public function exp()
    {
        return $this->payload['exp'];
    }

    /**
     * Get the issued at claim
     *
     * @return mixed|null
     */
    public function iat()
    {
        return $this->payload['iat'];
    }

    /**
     * Get the not before claim
     *
     * @return mixed|null
     */
    public function nbf()
    {
        return $this->payload['nbf'];
    }

    /**
     * Get the token id claim
     *
     * @return mixed|null
     */
    public function jti()
    {
        return $this->payload['jti'];
    }

    /**
     * Get the JWT payload
     *
     * @return array
     */
    public function toArray()
    {
        return $this->payload;
    }

    /**
     * Get the payload as an array
     *
     * @param integer $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->payload, $options);
    }

    /**
     * Get the payload as an array
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->payload;
    }

    /**
     * Get the payload as JSON
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Dynamically check a claim exist
     *
     * @param string $claim
     * @return bool
     */
    public function __isset(string $claim)
    {
        return $this->offsetExists($claim);
    }

    /**
     * Dynamically get a claim
     *
     * @param string $claim
     * @return mixed
     */
    public function __get(string $claim)
    {
        return $this->offsetGet($claim);
    }

    /**
     * Dynamically set a claim
     *
     * @param string $claim
     * @param mixed $value
     * @return void
     */
    public function __set(string $claim, $value)
    {
        $this->offsetSet($claim, $value);
    }

    /**
     * Dynamically unset a claim
     *
     * @param string $claim
     * @return void
     */
    public function __unset(string $claim)
    {
        $this->offsetUnset($claim);
    }

    /**
     * Determine if the offset exists
     *
     * @param string $key
     * @return void
     */
    public function offsetExists($key)
    {
        return isset($this->payload[$key]);
    }

    /**
     * Set the offset
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->payload[$key] ?? null;
    }

    /**
     * Set the offset
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->payload[$key] = $value;
    }

    /**
     * Unset the offset
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->payload[$key]);
    }
}
