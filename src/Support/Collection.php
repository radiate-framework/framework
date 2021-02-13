<?php

namespace Radiate\Support;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;

class Collection
{
    /**
     * The collection items
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create the collection instance
     *
     * @param array $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Create a collection instance
     *
     * @param array $items
     * @return static
     */
    public static function collect(array $items)
    {
        return new static($items);
    }

    /**
     * Add an item to the collection.
     *
     * @param mixed $item
     * @return static
     */
    public function add($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get the collection items
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Split a collection into chunks
     *
     * @param int $size
     * @return static
     */
    public function chunk(int $size)
    {
        $results = new static(array_chunk($this->items, $size, true));

        return $results->map(function ($chunk) {
            return new static($chunk);
        });
    }

    /**
     * Create a collection by using this collection for keys and another for its
     * values
     *
     * @param mixed $values
     * @return static
     */
    public function combine($values)
    {
        return new static(
            array_combine($this->items, $this->getArrayableItems($values))
        );
    }

    /**
     * Get the items in the collection that are not present in the given items
     *
     * @param mixed $items
     * @return static
     */
    public function diff($items)
    {
        return new static(
            array_diff($this->items, $this->getArrayableItems($items))
        );
    }

    /**
     * Execute a callback over each item
     *
     * @param callable $callback
     * @return static
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param string,... $keys
     * @return static
     */
    public function except(string ...$keys)
    {
        $new = clone $this;

        foreach ($keys as $key) {
            $new->forget($key);
        }

        return $new;
    }

    /**
     * Iterates over each item in the collection passing them to the callback
     * function
     *
     * @param callable|null $callback
     * @return static
     */
    public function filter(?callable $callback = null)
    {
        return new static(
            array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH)
        );
    }

    /**
     * Gets the first item of the collection
     *
     * @return mixed
     */
    public function first()
    {
        return $this->items[$this->firstKey()];
    }

    /**
     * Gets the first key of the collection
     *
     * @return string|int|null
     */
    public function firstKey()
    {
        return array_key_first($this->items);
    }

    /**
     * Flatten a multi-dimensional array into a single level
     *
     * @return static
     */
    public function flatten()
    {
        $new = clone $this;

        $results = [];

        array_walk_recursive($new->items, function ($value) use (&$results) {
            $results[] = $value;
        });

        return new static($results);
    }

    /**
     * Exchanges all keys with their associated values in a collection
     *
     * @return static
     */
    public function flip()
    {
        return new static(array_flip($this->items));
    }

    /**
     * Unset an item in the collection
     *
     * @param string $key
     * @return static
     */
    public function forget(string $key)
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Get an item from the collection
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default)
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Determine if an item in the collection exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Determine if the given key or index exists in the collection
     *
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Intersect the collection with the given items
     *
     * @param mixed $items
     * @return static
     */
    public function intersect($items)
    {
        return new static(
            array_intersect($this->items, $this->getArrayableItems($items))
        );
    }

    /**
     * Computes the intersection of the collection using keys for comparison
     *
     * @param mixed $items
     * @return static
     */
    public function intersectByKeys($items)
    {
        return new static(
            array_intersect_key($this->items, $this->getArrayableItems($items))
        );
    }

    /**
     * Determine if the collection is empty or not
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection is empty or not
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    /**
     * Join all items from the collection using a string.
     *
     * @param string $glue
     * @return string
     */
    public function join(string $glue): string
    {
        return implode($glue, $this->items);
    }

    /**
     * Get the keys from the collection
     *
     * @return static
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }

    /**
     * Gets the last item of the collection
     *
     * @return mixed
     */
    public function last()
    {
        return $this->items[$this->lastKey()];
    }

    /**
     * Gets the last key of the collection
     *
     * @return string|int|null
     */
    public function lastKey()
    {
        return array_key_last($this->items);
    }

    /**
     * Applies the callback to the elements of the collection
     *
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $items = array_map($callback, $this->items, $keys = array_keys($this->items));

        return new static(array_combine($keys, $items));
    }

    /**
     * Merge the collection with the given items
     *
     * @param mixed $array
     * @return static
     */
    public function merge($array)
    {
        return new static(
            array_merge($this->items, $this->getArrayableItems($array))
        );
    }

    /**
     * Recursively merge the collection with the given items
     *
     * @param mixed $array
     * @return static
     */
    public function mergeRecursive($array)
    {
        return new static(
            array_merge_recursive($this->items, $this->getArrayableItems($array))
        );
    }

    /**
     * Get the items with the specified keys
     *
     * @param string,... $keys
     * @return static
     */
    public function only(string ...$keys)
    {
        return new static($this->intersectByKeys(array_flip($keys))->all());
    }

    /**
     * Pass the collection to the given callback and return the result
     *
     * @param callable $callback
     * @return mixed
     */
    public function pipe(callable $callback)
    {
        return $callback($this);
    }

    /**
     * Get and remove the last item from the collection
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Push an item onto the beginning of the collection
     *
     * @param mixed $value
     * @param string|int|null $key
     * @return static
     */
    public function prepend($value, $key = null)
    {
        if ($key) {
            $this->items = [$key => $value] + $this->items;
        } else {
            array_unshift($this->items, $value);
        }

        return $this;
    }

    /**
     * Push elements onto the end of the collection
     *
     * @param mixed,... $values
     * @return static
     */
    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * Push keyed elements onto the end of the collection
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function put(string $key, $value)
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * Pick one or more random entries out of the collection
     *
     * @param int $number
     * @param bool $preserveKeys
     * @return mixed|static
     *
     * @throws \InvalidArgumentException
     */
    public function random(int $number = 1, bool $preserveKeys = false)
    {
        if ($number == 1) {
            return $this->items[array_rand($this->items)];
        }

        if ($number == 0) {
            return new static();
        }

        if ($number > $count = $this->count()) {
            throw new InvalidArgumentException(
                "You requested {$number} items, but there are only {$count} items available."
            );
        }

        $results = new static();

        foreach (array_rand($this->items, $number) as $key) {
            if ($preserveKeys) {
                $results[$key] = $this->items[$key];
            } else {
                $results[] = $this->items[$key];
            }
        }

        return $results;
    }

    /**
     * Reduce the collection to a single value
     *
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Replace the collection items with the given items
     *
     * @param mixed $items
     * @return static
     */
    public function replace($items)
    {
        return new static(
            array_replace($this->items, $this->getArrayableItems($items))
        );
    }

    /**
     * Recursively replace the collection items with the given items
     *
     * @param mixed $items
     * @return static
     */
    public function replaceRecursive($items)
    {
        return new static(
            array_replace_recursive($this->items, $this->getArrayableItems($items))
        );
    }

    /**
     * Return a collection with elements in reverse order
     *
     * @return static
     */
    public function reverse()
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Searches the collection for a given value and returns the corresponding
     * key if successful
     *
     * @param mixed $value
     * @param boolean $strict
     * @return int|string|false
     */
    public function search($value, bool $strict = false)
    {
        return array_search($value, $this->items, $strict);
    }

    /**
     * Shift an element off the beginning of the collection
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Shuffle the items in the collection
     *
     * @return static
     */
    public function shuffle()
    {
        $keys = array_keys($this->items);

        $results = new static();

        shuffle($keys);

        foreach ($keys as $key) {
            $results[$key] = $this->items[$key];
        }

        return $results;
    }

    /**
     * Skip the first x number of items
     *
     * @param int $count
     * @return static
     */
    public function skip(int $count)
    {
        return $this->slice($count);
    }

    /**
     * Extract a slice of the collection
     *
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function slice(int $offset, ?int $length = null)
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Take the first or last x number of items from the collection
     *
     * @param int $limit
     * @return static
     */
    public function take(int $limit)
    {
        return $this->slice($limit < 0 ? $limit : 0, abs($limit));
    }

    /**
     * Pass the collection to the given callback and then return it
     *
     * @param callable $callback
     * @return static
     */
    public function tap(callable $callback)
    {
        $callback(clone $this);

        return $this;
    }

    /**
     * Get the collection of items as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->map(function ($value) {
            return $value instanceof Collection
                ? $value->toArray()
                : $value;
        })->all();
    }

    /**
     * Get the collection of items as JSON
     *
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->items, $options);
    }

    /**
     * Return all the values of the collection
     *
     * @return static
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * Return an array of items from Collection or Traversable
     *
     * @param mixed $items
     * @return array
     */
    protected function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array) $items;
    }

    /**
     * Determine if an item in the collection exists
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item from the collection
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * Set an item in the collection
     *
     * @param string|null $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if ($key) {
            $this->items[$key] = $value;
        } else {
            $this->items[] = $value;
        }
    }

    /**
     * Unset an item in the collection
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    /**
     * Get the collection items count
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Implements \IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * JSON serialise the collection
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * Dynamically get an item from the collection
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->items[$key];
    }

    /**
     * Dynamically set items in the collection
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->items[$key] = $value;
    }

    /**
     * Return the collection as a JSON string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
