<?php

namespace Radiate\Database;

use Radiate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * Pluck the columns from the objects in the collection
     *
     * @param string|array $columns
     * @return static
     */
    public function only($columns)
    {
        return (new static($this))->map(function ($row) use ($columns) {

            $arr = [];

            foreach ((array) $columns as $column) {
                $arr[$column] = $row->$column;
            }

            return (object) $arr;
        });
    }

    /**
     * Pluck the columns from the objects in the collection
     *
     * @param string|array $columns
     * @return static
     */
    public function except($columns)
    {
        return (new static($this))->map(function ($row) use ($columns) {

            $columns = (array) $columns;

            $arr = [];

            foreach ($row->getAttributes() as $key => $prop) {
                if (!in_array($key, $columns)) {
                    $arr[$key] = $prop;
                }
            }

            return (object) $arr;
        });
    }
}
