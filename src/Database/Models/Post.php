<?php

namespace Radiate\Database\Models;

use DateTimeImmutable;
use Radiate\Database\Concerns\HasMeta;
use Radiate\Database\Model;
use Radiate\Database\PostTypeQueryBuilder;
use WP_Post;

class Post extends Model
{
    use HasMeta;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The object type for the model.
     *
     * @var string
     */
    protected $objectType = 'post';

    /**
     * The post type
     *
     * @var string
     */
    protected static $postType = 'post';

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    public static function booted()
    {
        static::addGlobalScope('post_type', function ($builder) {
            $builder->where('post_type', static::$postType);
        });
    }

    /**
     * Create a new query builder for the model.
     *
     * @return \Radiate\Database\Builder
     */
    public function newQueryBuilder()
    {
        return new PostTypeQueryBuilder();
    }

    /**
     * Perform a model update operation.
     *
     * @return bool
     */
    protected function performUpdate()
    {
        return !is_wp_error(wp_update_post($this->getAttributes(), true));
    }

    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function performInsert()
    {
        $this->setAttribute('post_type', static::$postType);

        $id = wp_insert_post($this->getAttributes(), true);

        if (!is_wp_error($id)) {
            $this->fill(get_post($id)->to_array());

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
        return wp_delete_post($this->getKey()) instanceof WP_Post;
    }

    /**
     * Get the ID
     *
     * @return integer|null
     */
    public function getIdAttribute(): ?int
    {
        return $this->attributes['ID'];
    }

    /**
     * Set the ID
     *
     * @param integer $value
     * @return void
     */
    public function setIdAttribute(int $value): void
    {
        $this->attributes['ID'] = $value;
    }

    /**
     * Get the title
     *
     * @return string|null
     */
    public function getTitleAttribute(): ?string
    {
        return $this->attributes['post_title'];
    }

    /**
     * Set the title
     *
     * @param string $value
     * @return void
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['post_title'] = $value;
    }

    /**
     * Get the status
     *
     * @return string|null
     */
    public function getStatusAttribute(): ?string
    {
        return $this->attributes['post_status'];
    }

    /**
     * Set the status
     *
     * @param string $value
     * @return void
     */
    public function setStatusAttribute(string $value): void
    {
        $this->attributes['post_status'] = $value;
    }

    /**
     * Get the slug
     *
     * @return string|null
     */
    public function getSlugAttribute(): ?string
    {
        return $this->attributes['post_name'];
    }

    /**
     * Set the slug
     *
     * @param string $value
     * @return void
     */
    public function setSlugAttribute(string $value): void
    {
        $this->attributes['post_name'] = $value;
    }

    /**
     * Get the content
     *
     * @return string|null
     */
    public function getContentAttribute(): ?string
    {
        return $this->attributes['post_content'];
    }

    /**
     * Set the content
     *
     * @param string $value
     * @return void
     */
    public function setContentAttribute(string $value): void
    {
        $this->attributes['post_content'] = $value;
    }

    /**
     * Get the excerpt
     *
     * @return string|null
     */
    public function getExcerptAttribute(): ?string
    {
        return $this->attributes['post_excerpt'];
    }

    /**
     * Set the excerpt
     *
     * @param string $value
     * @return void
     */
    public function setExcerptAttribute(string $value): void
    {
        $this->attributes['post_excerpt'] = $value;
    }

    /**
     * Get the registered attribute
     *
     * @return \DateTimeImmutable
     */
    public function getCreatedAtAttribute()
    {
        return new DateTimeImmutable($this->attributes['post_date']);
    }

    /**
     * Set the registered attribute
     *
     * @param \DateTimeImmutable  $value
     * @return void
     */
    public function setCreatedAtAttribute(DateTimeImmutable $value)
    {
        $this->attributes['post_date'] = $value->format('Y-m-d H:i:s');
    }

    /**
     * Get the registered attribute
     *
     * @return \DateTimeImmutable
     */
    public function getUpdatedAtAttribute()
    {
        return new DateTimeImmutable($this->attributes['post_modified']);
    }

    /**
     * Set the registered attribute
     *
     * @param \DateTimeImmutable  $value
     * @return void
     */
    public function setUpdatedAtAttribute(DateTimeImmutable $value)
    {
        $this->attributes['post_modified'] = $value->format('Y-m-d H:i:s');
    }

    /**
     * Get the permalink
     *
     * @return string
     */
    public function permalink()
    {
        return get_permalink($this->getKey());
    }
}
