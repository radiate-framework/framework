<?php

namespace Radiate\Support\Facades;

use Radiate\Support\Collection;

/**
 * @method static \Radiate\Support\Collection add(array $items, mixed $item) Add an item to the collection.
 * @method static array all(array $items) Get the collection items
 * @method static \Radiate\Support\Collection chunk(array $items, int $size) Split a collection into chunks
 * @method static \Radiate\Support\Collection combine(array $items, mixed $values) Create a collection by using this collection for keys and another for its values
 * @method static \Radiate\Support\Collection diff(array $items, mixed $items) Get the items in the collection that are not present in the given items
 * @method static \Radiate\Support\Collection each(array $items, callable $callback) Execute a callback over each item
 * @method static \Radiate\Support\Collection except(array $items, string ...$keys) Get all items except for those with the specified keys.
 * @method static \Radiate\Support\Collection filter(array $items, ?callable $callback = null) Iterates over each item in the collection passing them to the callback function
 * @method static mixed first(array $items) Gets the first item of the collection
 * @method static string|int|null firstKey(array $items) Gets the first key of the collection
 * @method static \Radiate\Support\Collection flatten(array $items) Flatten a multi-dimensional array into a single level
 * @method static \Radiate\Support\Collection flip(array $items) Exchanges all keys with their associated values in a collection
 * @method static \Radiate\Support\Collection forget(array $items, string $key) Unset an item in the collection
 * @method static mixed get(array $items, string $key, mixed $default) Get an item from the collection
 * @method static bool has(array $items, string $key) Determine if an item in the collection exists
 * @method static bool hasKey(array $items, string $key) Determine if the given key or index exists in the collection
 * @method static \Radiate\Support\Collection intersect(array $items, mixed $items) Intersect the collection with the given items
 * @method static \Radiate\Support\Collection intersectByKeys(array $items, mixed $items) Computes the intersection of the collection using keys for comparison
 * @method static bool isEmpty(array $items) Determine if the collection is empty or not
 * @method static bool isNotEmpty(array $items) Determine if the collection is empty or not
 * @method static string join(array $items, string $glue) Join all items from the collection using a string.
 * @method static \Radiate\Support\Collection keys(array $items) Get the keys from the collection
 * @method static mixed last(array $items) Gets the last item of the collection
 * @method static string|int|null lastKey(array $items) Gets the last key of the collection
 * @method static \Radiate\Support\Collection map(array $items, callable $callback) Applies the callback to the elements of the collection
 * @method static \Radiate\Support\Collection merge(array $items, mixed $array) Merge the collection with the given items
 * @method static \Radiate\Support\Collection mergeRecursive(array $items, mixed $array) Recursively merge the collection with the given items
 * @method static \Radiate\Support\Collection only(array $items, string ...$keys) Get the items with the specified keys
 * @method static mixed pipe(array $items, callable $callback) Pass the collection to the given callback and return the result
 * @method static mixed pop(array $items) Get and remove the last item from the collection
 * @method static \Radiate\Support\Collection prepend(array $items, mixed $value, string|int|null $key = null) Push an item onto the beginning of the collection
 * @method static \Radiate\Support\Collection push(array $items, mixed ...$values) Push elements onto the end of the collection
 * @method static \Radiate\Support\Collection put(array $items, string $key, mixed $value) Push keyed elements onto the end of the collection
 * @method static mixed|static random(array $items, int $number = 1, bool $preserveKeys = false) Pick one or more random entries out of the collection
 * @method static mixed reduce(array $items, callable $callback, mixed $initial = null) Reduce the collection to a single value
 * @method static \Radiate\Support\Collection replace(array $items, mixed $items) Replace the collection items with the given items
 * @method static \Radiate\Support\Collection replaceRecursive(array $items, mixed $items) Recursively replace the collection items with the given items
 * @method static \Radiate\Support\Collection reverse(array $items) Return a collection with elements in reverse order
 * @method static int|string|false search(array $items, mixed $value, bool $strict = false) Searches the collection for a given value and returns the corresponding key if successful
 * @method static mixed shift(array $items) Shift an element off the beginning of the collection
 * @method static \Radiate\Support\Collection shuffle(array $items) Shuffle the items in the collection
 * @method static \Radiate\Support\Collection skip(array $items, int $count) Skip the first x number of items
 * @method static \Radiate\Support\Collection slice(array $items, int $offset, ?int $length = null) Extract a slice of the collection
 * @method static \Radiate\Support\Collection take(array $items, int $limit) Take the first or last x number of items from the collection
 * @method static \Radiate\Support\Collection tap(array $items, callable $callback) Pass the collection to the given callback and then return it
 * @method static \Radiate\Support\Collection values(array $items) Return all the values of the collection
 *
 * @see \Radiate\Support\Collection
 */
class Arr
{
    /**
     * Make a new instance of Collection
     *
     * @param array $array
     * @return \Radiate\Support\Collection
     */
    public static function collect(array $array = [])
    {
        return new Collection($array);
    }

    /**
     * Dynamically call the Collection class
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $arr = (new Collection(array_shift($parameters)))->$method(...$parameters);

        return $arr instanceof Collection ? $arr->toArray() : $arr;
    }
}
