<?php

namespace Radiate\Database;

use JsonSerializable;
use Radiate\Support\Collection;
use Stringable;

class Option implements JsonSerializable, Stringable
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
     * Get many options
     *
     * @param array $keys
     * @param mixed|null $default
     * @return array
     */
    public function getMany(array $keys, $default = null): array
    {
        $return = [];

        $keys = Collection::make($keys)->mapWithKeys(function ($value, $key) use ($default) {
            return [is_string($key) ? $key : $value => is_string($key) ? $value : $default];
        })->all();

        foreach ($keys as $key => $default) {
            $return[$key] = $this->get($key, $default);
        }

        return $return;
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
     * Set many options
     *
     * @param array $options
     * @return boolean
     */
    public function setMany(array $options): bool
    {
        $result = true;

        foreach ($options as $key => $value) {
            if (!$this->set($key, $value)) {
                $result = false;
            }
        }

        return $result;
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
     * Delete multiple keys
     *
     * @param array $keys
     * @return boolean
     */
    public function deleteMany(array $keys): bool
    {
        $result = true;

        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Get all the options
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
