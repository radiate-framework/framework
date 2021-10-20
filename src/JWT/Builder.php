<?php

namespace Radiate\JWT;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Stringable;

class Builder implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    /**
     * The JWT manager
     *
     * @var \Radiate\JWT\Manager
     */
    protected $manager;

    /**
     * The JWT claims
     *
     * @var array
     */
    protected $claims = [];

    /**
     * Create the JWT manager instance
     *
     * @param \Radiate\JWT\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Set the issuer claim
     *
     * @param mixed $issuer
     * @return static
     */
    public function iss($issuer)
    {
        $this->claims['iss'] = $issuer;

        return $this;
    }

    /**
     * Set the audience claim
     *
     * @param mixed $audience
     * @return static
     */
    public function aud($audience)
    {
        $this->claims['aud'] = $audience;

        return $this;
    }

    /**
     * Set the subject claim
     *
     * @param mixed $subject
     * @return static
     */
    public function sub($subject)
    {
        $this->claims['sub'] = $subject;

        return $this;
    }

    /**
     * Set the expiry claim
     *
     * @param mixed $expiry
     * @return static
     */
    public function exp($time)
    {
        $this->claims['exp'] = $time;

        return $this;
    }

    /**
     * Set the expiry claim
     *
     * @param mixed $expiry
     * @return static
     */
    public function iat($time)
    {
        $this->claims['iat'] = $time;

        return $this;
    }

    /**
     * Set the expiry claim
     *
     * @param mixed $expiry
     * @return static
     */
    public function nbf($time)
    {
        $this->claims['nbf'] = $time;

        return $this;
    }

    /**
     * Set the id claim
     *
     * @param mixed $id
     * @return static
     */
    public function jti($id)
    {
        $this->claims['jti'] = $id;

        return $this;
    }

    /**
     * Set a custom claim
     *
     * @param string $claim
     * @param mixed $value
     * @return static
     */
    public function with(string $claim, $value)
    {
        $this->claims[$claim] = $value;

        return $this;
    }

    /**
     * Add the claims to the JWT
     *
     * @param array $claims
     * @return static
     */
    public function claims(array $claims)
    {
        $this->claims = $claims;

        return $this;
    }

    /**
     * Merge the claims
     *
     * @param array $claims
     * @return static
     */
    public function withClaims(array $claims)
    {
        $this->claims = array_merge($this->claims, $claims);

        return $this;
    }

    /**
     * Encode the JWT
     *
     * @return string
     */
    public function encode(): string
    {
        return $this->manager->encode($this->claims);
    }

    /**
     * Get the JWT claims
     *
     * @return array
     */
    public function toArray()
    {
        return $this->claims;
    }

    /**
     * Return the JWT claims as JSON
     *
     * @param integer $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Encode the JWT when serialized
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->encode();
    }

    /**
     * Return the encoded JWT
     *
     * @return string
     */
    public function __toString()
    {
        return $this->encode();
    }
}
