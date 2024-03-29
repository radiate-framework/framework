<?php

namespace Radiate\Database;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Radiate\Database\Concerns\HasAttributes;
use Radiate\Database\Concerns\HasGlobalScopes;
use RuntimeException;
use Stringable;

class Model implements Arrayable, ArrayAccess, Jsonable, JsonSerializable, Stringable
{
    use HasAttributes;
    use HasGlobalScopes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The object type
     *
     * @var string
     */
    protected $objectType = '';

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Indicates if the model was inserted during the current request lifecycle.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['*'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();

        $this->syncOriginal();

        $this->fill($attributes);
    }

    /**
     * Check if the model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        if (!isset(static::$booted[static::class])) {
            static::$booted[static::class] = true;
            static::boot();
            static::booted();
        }
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        //
    }

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        //
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the model's object type.
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Get all of the models from the database.
     *
     * @param array|mixed $columns
     * @return \Radiate\Database\Collection
     */
    public static function all()
    {
        return static::query()->get();
    }

    /**
     * Begin querying the model.
     *
     * @return \Radiate\Database\Builder
     */
    public static function query()
    {
        return (new static)->getQueryBuilder();
    }

    /**
     * Create a new query builder for the model.
     *
     * @return \Radiate\Database\Builder
     */
    public function newQueryBuilder()
    {
        return new Builder();
    }

    /**
     * Get the query builder instance
     *
     * @return \Radiate\Database\Builder
     */
    public function getQueryBuilder()
    {
        return $this->registerGlobalScopes($this->newQueryBuilder())
            ->setModel($this);
    }

    /**
     * Create a new instance of the model
     *
     * @param array $attributes
     * @param boolean $exists
     * @return static
     */
    public function newInstance(array $attributes = [], bool $exists = false)
    {
        $model = new static((array) $attributes);

        $model->exists = $exists;

        return $model;
    }

    /**
     * Create a new instance from the builder
     *
     * @param array $attributes
     * @return static
     */
    public function newFromBuilder(array $attributes = [])
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        return $model;
    }

    /**
     * Create a new collection of models
     *
     * @param array $models
     * @return \Radiate\Database\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save()
    {
        if ($this->exists) {
            $saved = $this->isDirty() ? $this->performUpdate() : true;
        } else {
            $saved = $this->performInsert();

            $this->wasRecentlyCreated = $this->exists = $saved;
        }

        if ($saved) {
            $this->syncOriginal();
        }

        return $saved;
    }

    /**
     * Perform a model update operation.
     *
     * @return bool
     */
    protected function performUpdate()
    {
        return true;
    }

    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function performInsert()
    {
        return true;
    }

    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function performDelete()
    {
        return true;
    }

    /**
     * Update the model in the database.
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes = [])
    {
        if (!$this->exists) {
            return false;
        }

        return $this->fill($attributes)->save();
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     */
    public function delete()
    {
        if (!$this->exists) {
            return;
        }

        $this->exists = false;

        return $this->performDelete();
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return static
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } else {
                throw new RuntimeException(sprintf(
                    'Add [%s] to fillable property to allow mass assignment on [%s].',
                    $key,
                    get_class($this)
                ));
            }
        }

        return $this;
    }

    /**
     * Set the fillable attributes for the model.
     *
     * @param  array  $fillable
     * @return $this
     */
    public function fillable(array $fillable)
    {
        $this->fillable = $fillable;

        return $this;
    }

    /**
     * Determine if the attribute is fillable
     *
     * @param string $key
     * @return boolean
     */
    protected function isFillable(string $key)
    {
        if (in_array($key, $this->fillable)) {
            return true;
        }

        if (in_array($key, $this->guarded) || in_array('*', $this->guarded)) {
            return false;
        }

        return empty($this->fillable);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
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
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
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
        $this->setAttribute($offset, $value);
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
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAttributes();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
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

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters = [])
    {
        return $this->getQueryBuilder()->$method(...$parameters);
    }

    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters = [])
    {
        return (new static)->$method(...$parameters);
    }
}
