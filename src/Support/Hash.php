<?php

namespace Radiate\Support;

class Hash
{
    /**
     * Hash the string
     *
     * @param string $value
     * @return string
     */
    public static function make(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * Verify a value and hash match
     *
     * @param string $value
     * @param string $hash
     * @return bool
     */
    public static function check(string $value, string $hash): bool
    {
        if (strlen($hash) === 0) {
            return false;
        }
        return password_verify($value, $hash);
    }
}
