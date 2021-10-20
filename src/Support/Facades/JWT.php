<?php

namespace Radiate\Support\Facades;

use Radiate\JWT\Manager;

/**
 * @method static \Radiate\JWT\Builder iss(mixed $issuer)Set the iss claim
 * @method static \Radiate\JWT\Builder aud(mixed $audience) Set the aud claim
 * @method static \Radiate\JWT\Builder sub(mixed $subject) Set the sub claim
 * @method static \Radiate\JWT\Builder exp(mixed $time) Set the exp claim
 * @method static \Radiate\JWT\Builder iat(mixed $time) Set the iat claim
 * @method static \Radiate\JWT\Builder nbf(mixed $time) Set the nbf claim
 * @method static \Radiate\JWT\Builder jti(mixed $id) Set the jti claim
 * @method static \Radiate\JWT\Builder with(string $claim, mixed $value) Set a custom claim
 * @method static \Radiate\JWT\Builder claims(array $claims) Set the JWT claims
 * @method static \Radiate\JWT\Builder withClaims(array $claims) Merge the claims
 * @method static string encode(mixed $payload) Converts and signs a PHP object or array into a JWT string.
 * @method static \Radiate\JWT\Payload decode(string|null $jwt) Decodes a JWT string into a Payload instance
 * @method static string|null token(\Radiate\Http\Request $request) Get the JWT from the request
 * @method static \Radiate\JWT\Payload|null payload(\Radiate\Http\Request $request) Get the JWT payload from the request
 *
 * @see \Radiate\JWT\Manager
 * @see \Radiate\JWT\Builder
 */
class JWT extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
