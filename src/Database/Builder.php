<?php

namespace Radiate\Database;

use Closure;

class Builder
{
    /**
     * The builder's model
     *
     * @var \Radiate\Database\Model
     */
    protected $model;

    /**
     * Applied global scopes.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Removed global scopes.
     *
     * @var array
     */
    protected $removedScopes = [];

    /**
     * Register a new global scope.
     *
     * @param string $identifier
     * @param \Closure $scope
     * @return $this
     */
    public function withGlobalScope(string $identifier, Closure $scope)
    {
        $this->scopes[$identifier] = $scope;

        return $this;
    }

    /**
     * Remove a registered global scope.
     *
     * @param string $scope
     * @return $this
     */
    public function withoutGlobalScope($scope)
    {
        unset($this->scopes[$scope]);

        $this->removedScopes[] = $scope;

        return $this;
    }

    /**
     * Remove all or passed registered global scopes.
     *
     * @param  array|null  $scopes
     * @return $this
     */
    public function withoutGlobalScopes(array $scopes = null)
    {
        if (!is_array($scopes)) {
            $scopes = array_keys($this->scopes);
        }

        foreach ($scopes as $scope) {
            $this->withoutGlobalScope($scope);
        }

        return $this;
    }

    /**
     * Apply the scopes to the builder instance and return it.
     *
     * @return static
     */
    protected function applyScopes()
    {
        if (!$this->scopes) {
            return $this;
        }

        $builder = clone $this;

        foreach ($this->scopes as $identifier => $scope) {
            if (!isset($builder->scopes[$identifier])) {
                continue;
            }

            $scope($builder);
        }

        return $builder;
    }

    /**
     * Get the model
     *
     * @return \Radiate\Database\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the model
     *
     * @param \Radiate\Database\Model $model
     * @return void
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get a new model instance
     *
     * @param array $attributes
     * @return mixed
     */
    protected function newModelInstance($attributes = [])
    {
        return $this->model->newInstance($attributes);
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param  array  $items
     * @return \Radiate\Database\Collection
     */
    public function hydrate(array $items)
    {
        $instance = $this->newModelInstance();

        return $instance->newCollection($items)->map(function ($item) use ($instance) {
            return $instance->newFromBuilder($item->to_array());
        });
    }

    /**
     * Create a new model instance
     *
     * @param array $attributes
     * @return \Radiate\Database\Model
     */
    public function create(array $attributes)
    {
        $model = $this->newModelInstance($attributes);

        $model->save();

        return $model;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array|string $column
     * @return \Radiate\Database\Collection
     */
    public function get()
    {
        $builder = $this->applyScopes();

        $models = $builder->getModels();

        return $builder->getModel()->newCollection($models);
    }

    /**
     * Get the hydrated models.
     *
     * @param array|string $column
     * @return \Radiate\Database\Model[]|array
     */
    protected function getModels()
    {
        return $this->model->hydrate([])->all();
    }

    /**
     * Run a "raw" WordPress style query instead of using the fluent interface.
     *
     * @param array $query
     * @return \Radiate\Database\Collection
     */
    public function rawQuery(array $query)
    {
        $this->bindings = $query;

        return $this->withoutGlobalScopes()->get();
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
     * Order by the latest
     *
     * @param string $column
     * @return static
     */
    public function latest(string $column = 'term_id')
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Order by the oldest
     *
     * @param string $column
     * @return static
     */
    public function oldest(string $column = 'term_id')
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * Get the specified columns from the results
     *
     * @param string|array $columns
     * @return \Radiate\Database\Collection
     */
    public function only($columns)
    {
        return $this->get()->only($columns);
    }

    /**
     * Get the first record
     *
     * @param string $column
     * @return static
     */
    public function first()
    {
        return $this->take(1)->get()->first();
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (method_exists($this->model, $scope = 'scope' . ucfirst($method))) {
            return $this->model->$scope($this, ...$parameters);
        }

        return $this;
    }
}
