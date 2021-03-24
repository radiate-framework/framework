<?php

namespace Radiate\Database;

use WP_User_Query;

class UserQueryBuilder extends Builder
{
    /**
     * The query object
     *
     * @var \WP_User_Query
     */
    protected $query;

    /**
     * The query bindings
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Create the builder instance
     */
    public function __construct()
    {
        $this->query = new WP_User_Query();
    }

    /**
     * Get the hydrated models.
     *
     * @param array|string $column
     * @return \Radiate\Database\Model[]|array
     */
    protected function getModels()
    {
        $this->query->prepare_query($this->bindings);

        $this->query->query();

        return $this->model->hydrate($this->query->get_results())->all();
    }

    /**
     * Find a field
     *
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public function where(string $field, $value)
    {
        $this->bindings[$field] = $value;

        return $this;
    }

    /**
     * Find within an array
     *
     * @param string $field
     * @param array $values
     * @return static
     */
    public function whereIn(string $field, array $values)
    {
        $this->bindings[$field] = $values;

        return $this;
    }

    /**
     * Order by a field
     *
     * @param string $field
     * @param string $direction
     * @return static
     */
    public function orderBy(string $field, string $direction = 'asc')
    {
        return $this->where('orderby', $field)
            ->where('order', strtoupper($direction));
    }

    /**
     * Limit the number of results
     *
     * @param integer $limit
     * @return static
     */
    public function limit(int $limit)
    {
        return $this->where('number', $limit);
    }

    /**
     * Alias for limit
     *
     * @param integer $limit
     * @return static
     */
    public function take(int $limit)
    {
        return $this->limit($limit);
    }

    /**
     * Offset the results
     *
     * @param integer $limit
     * @return static
     */
    public function offset(int $limit)
    {
        return $this->where('offset', $limit);
    }

    /**
     * Alias for offset
     *
     * @param integer $limit
     * @return static
     */
    public function skip(int $limit)
    {
        return $this->offset($limit);
    }

    /**
     * Find by the ID
     *
     * @param mixed $id
     * @param string $column
     * @return static
     */
    public function find($id)
    {
        return $this->where('include', $id)->first();
    }

    /**
     * Order by the latest
     *
     * @param string $column
     * @return static
     */
    public function latest(string $column = 'registered')
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Order by the oldest
     *
     * @param string $column
     * @return static
     */
    public function oldest(string $column = 'registered')
    {
        return $this->orderBy($column, 'asc');
    }
}
