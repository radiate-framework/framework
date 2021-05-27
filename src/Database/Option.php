<?php

namespace Radiate\Database;

use JsonSerializable;

class Option implements JsonSerializable
{
    /**
     * Determine if the option exists
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return get_option($key) !== false;
    }

    /**
     * Get an option
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return get_option($key, $default);
    }

    /**
     * Set an option
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function set(string $key, $value): bool
    {
        return update_option($key, $value);
    }

    /**
     * Delete an option
     *
     * @param string $key
     * @return boolean
     */
    public function delete(string $key): bool
    {
        return delete_option($key);
    }

    /**
     * Get all the otpinos
     *
     * @return array
     */
    public function all()
    {
        return array_map('maybe_unserialize', wp_load_alloptions());
    }

    /**
     * JSON serialise the options array
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->all();
    }

    /**
     * Get a JSON encoded representation of the options
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->all());
    }
}
