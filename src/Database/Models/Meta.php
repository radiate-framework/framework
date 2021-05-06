<?php

namespace Radiate\Database\Models;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Radiate\Database\Concerns\HasAttributes;
use Radiate\Database\Model;

class Meta implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    use HasAttributes;

    /**
     * The model instance
     *
     * @var \Radiate\Database\Model
     */
    protected $model;

    /**
     * Create the meta instance
     *
     * @param array $attributes
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Hydrate the attributes
     *
     * @return static
     */
    public function hydrate()
    {
        $meta = get_metadata($this->model->getObjectType(), $this->model->getKey()) ?? [];

        $this->setRawAttributes($this->unserializeMeta($meta), true);

        return $this;
    }

    /**
     * Fill the attributes
     *
     * @param  array  $attributes
     * @return static
     */
    public function fill(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     */
    public function save()
    {
        $saved = $this->isDirty() ? $this->performUpdate() : true;

        if ($saved) {
            $this->syncOriginal();
        }

        return $saved;
    }

    /**
     * Update the model in the database.
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes = [])
    {
        return $this->fill($attributes)->save();
    }

    /**
     * Perform a model update operation.
     *
     * @return bool
     */
    protected function performUpdate()
    {
        foreach ($this->getDirty() as $key => $value) {
            update_metadata(
                $this->model->getObjectType(),
                (int) $this->model->getKey(),
                $key,
                $value
            );
        }

        return true;
    }

    /**
     * Delete the model from the database.
     *
     * @param string|array $keys
     * @return bool|null
     */
    public function delete($keys)
    {
        foreach ((array) $keys as $key) {
            $this->offsetUnset($key);

            delete_metadata(
                $this->model->getObjectType(),
                (int) $this->model->getKey(),
                $key
            );
        }

        return true;
    }

    /**
     * Unserialize the object meta
     *
     * @param array $meta
     * @return array
     */
    protected function unserializeMeta(array $meta)
    {
        $unserialized = [];

        foreach ($meta as $key => $values) {
            $newValues = [];

            foreach ($values as $value) {
                $newValues[] = maybe_unserialize($value);
            }

            $unserialized[$key] = count($newValues) !== 1 ? $newValues : $newValues[0];
        }

        return $unserialized;
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->attributes);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset] ?? null;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson(int $options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
