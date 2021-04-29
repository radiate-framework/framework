<?php

namespace Radiate\Database\Models;

use Radiate\Database\Concerns\HasMeta;
use Radiate\Database\Model;
use Radiate\Database\TermQueryBuilder;

class Term extends Model
{
    use HasMeta;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * The object type for the model.
     *
     * @var string
     */
    protected $objectType = 'term';

    /**
     * The taxonomy
     *
     * @var string
     */
    protected static $taxonomy;

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    public static function booted()
    {
        static::addGlobalScope('taxonomy', function ($builder) {
            $builder->where('taxonomy', static::$taxonomy)->where('get', 'all');
        });
    }

    /**
     * Create a new query builder for the model.
     *
     * @return \Radiate\Database\Builder
     */
    public function newQueryBuilder()
    {
        return new TermQueryBuilder();
    }

    /**
     * Perform a model update operation.
     *
     * @return bool
     */
    protected function performUpdate()
    {
        return !is_wp_error(
            wp_update_term($this->getKey(), static::$taxonomy, $this->getAttributes())
        );
    }

    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function performInsert()
    {
        $attributes = wp_insert_term(
            $this->name,
            static::$taxonomy,
            $this->getAttributes()
        );

        if (!is_wp_error($attributes)) {
            $this->fill($attributes);

            return true;
        }

        return false;
    }

    /**
     * Perform a model delete operation.
     *
     * @return bool
     */
    protected function performDelete()
    {
        return !is_wp_error(wp_delete_term($this->getKey(), static::$taxonomy));
    }

    /**
     * Get the ID
     *
     * @return integer|null
     */
    public function getIdAttribute(): ?int
    {
        return $this->attributes['term_id'];
    }

    /**
     * Set the ID
     *
     * @param integer $value
     * @return void
     */
    public function setIdAttribute(int $value): void
    {
        $this->attributes['term_id'] = $value;
    }
    
    /**
     * Get the tag permalink
     *
     * @return string
     */
    public function permalink()
    {
        return get_term_link($this->getKey(), static::$taxonomy);
    }
}
