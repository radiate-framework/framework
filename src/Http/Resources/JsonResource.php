<?php

namespace Radiate\Http\Resources;

use ArrayAccess;
use JsonSerializable;
use Radiate\Http\Request;
use Radiate\Support\Collection;
use Radiate\Support\Facades\App;
use Stringable;

abstract class JsonResource implements ArrayAccess, JsonSerializable, Stringable
{
    /**
     * The proxied resource
     *
     * @var mixed
     */
    protected $resource;

    /**
     * Cerate the resource instance
     *
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Create new resource collection.
     *
     * @param  mixed  $resource
     * @return \Radiate\Support\Collection
     */
    public static function collection($resources)
    {
        return (new Collection($resources))->map(function ($resource) {
            return new static($resource);
        });
    }

    /**
     * Resolve the resource to an array.
     *
     * @param  \Radiate\Http\Request|null  $request
     * @return array
     */
    public function resolve(?Request $request = null)
    {
        $data = $this->toArray(
            $request ?? App::getInstance()->make('request')
        );

        if ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return (array) $data;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Radiate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request)
    {
        if (is_null($this->resource)) {
            return [];
        }

        return is_array($this->resource)
            ? $this->resource
            : $this->resource->toArray();
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->resolve(App::getInstance()->make('request'));
    }

    /**
     * Transform the resource into a JSON string
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->resource[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->resource[$offset];
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
        $this->resource[$offset] = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->resource[$offset]);
    }

    /**
     * Determine if an attribute exists on the resource.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->resource->{$key});
    }

    /**
     * Unset an attribute on the resource.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->resource->{$key});
    }

    /**
     * Dynamically get properties from the underlying resource.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->resource->{$key};
    }

    /**
     * Dynamically pass method calls to the underlying resource.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func([$this->resource, $method], ...$parameters);
    }
}
