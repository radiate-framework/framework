<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Radiate\Auth\JWT\Builder issuer(mixed $issuer) Set the issuer of the JWT.
 * @method static \Radiate\Auth\JWT\Builder subject(mixed $subject) Set the subject of the JWT (the user).
 * @method static \Radiate\Auth\JWT\Builder audience(mixed $audience) Set the recipient for which the JWT is intended.
 * @method static \Radiate\Auth\JWT\Builder expirationTime(int $time) Set the time after which the JWT expires.
 * @method static \Radiate\Auth\JWT\Builder notBeforeTime(int $time) Set the time before which the JWT must not be accepted for processing.
 * @method static \Radiate\Auth\JWT\Builder issuedAtTime(int $time) Set the time at which the JWT was issued; can be used to determine age of the JWT.
 * @method static \Radiate\Auth\JWT\Builder id(int|string $id) Set the unique identifier; can be used to prevent the JWT from being replayed (allows a token to be used only once).
 * @method static string encode() Encode the JWT.
 * @method static \Radiate\Auth\JWT\Response decode(string $token) Decode the token.
 *
 * @see \Radiate\Auth\JWT\Builder
 */
class JWT extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'jwt';
    }
}
