<?php

namespace Radiate\Http\Resources;

use Radiate\Http\Request;
use Radiate\Http\Resources\JsonResource;
use Radiate\Support\Collection;
use Radiate\Support\Str;

abstract class ResourceCollection extends JsonResource
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects;

    /**
     * The mapped collection instance.
     *
     * @var \Radiate\Support\Collection
     */
    public $collection;

    /**
     * Cerate the resource collection instance
     *
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }

    /**
     * Map the given collection resource into its individual resources.
     *
     * @param  mixed  $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if (is_array($resource)) {
            $resource = new Collection($resource);
        }

        $collects = $this->collects();

        $this->collection = $collects && !$resource->first() instanceof $collects
            ? $resource->mapInto($collects)
            : $resource->toBase();

        return $this->collection;
    }

    /**
     * Get the resource that this resource collects.
     *
     * @return string|null
     */
    protected function collects()
    {
        if ($this->collects) {
            return $this->collects;
        }

        if (
            Str::endsWith($name = get_class($this), 'Collection') &&
            class_exists($class = Str::replaceLast($name, 'Collection', ''))
        ) {
            return $class;
        }
    }

    /**
     * Return the count of items in the resource collection.
     *
     * @return int
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Transform the resource into a JSON array.
     *
     * @param \Radiate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return $this->collection->map->toArray($request)->all();
    }
}
