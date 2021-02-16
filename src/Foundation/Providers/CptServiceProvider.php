<?php

namespace Radiate\Foundation\Providers;

use Radiate\Support\ServiceProvider;

class CptServiceProvider extends ServiceProvider
{
    /**
     * The custom post types to register
     *
     * @var array
     */
    protected $postTypes = [];

    /**
     * Boot the app post types
     *
     * @return void
     */
    public function boot(): void
    {
        foreach ($this->postTypes as $postType => $taxonomies) {
            $cpt = call_user_func([new $postType(), 'register']);

            foreach (array_unique($taxonomies) as $taxonomy) {
                call_user_func([new $taxonomy($cpt), 'register']);
            }
        }
    }
}
