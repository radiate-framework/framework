<?php

namespace Radiate\Database\Models;

use WP_Post;

class Attachment extends Post
{
    /**
     * The post type
     *
     * @var string
     */
    protected static $postType = 'attachment';

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    public static function booted()
    {
        parent::booted();

        static::addGlobalScope('post_status', function ($builder) {
            $builder->where('post_status', 'inherit');
        });
    }

    /**
     * Get the attachment URL
     *
     * @return string
     */
    public function url()
    {
        return wp_get_attachment_url($this->getKey());
    }

    /**
     * Get the attachment file path
     *
     * @return string
     */
    public function filePath()
    {
        return get_attached_file($this->getKey());
    }

    /**
     * Get the attachment image URL
     *
     * @param string|int[] $size
     * @return string
     */
    public function image($size = 'thumbnail')
    {
        [$url, $width, $height] = wp_get_attachment_image_src($this->getKey(), $size);

        return (object) compact('url', 'width', 'height');
    }

    /**
     * Perform a model delete operation.
     *
     * @return bool
     */
    protected function performDelete()
    {
        return wp_delete_attachment($this->getKey(), true) instanceof WP_Post;
    }
}
