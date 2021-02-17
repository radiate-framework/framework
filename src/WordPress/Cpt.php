<?php

namespace Radiate\WordPress;

use Radiate\WordPress\Core\Category;
use Radiate\WordPress\Core\PostTag;

abstract class Cpt
{
    /**
     * The name
     *
     * @var string
     */
    protected $name;

    /**
     * The singular label
     *
     * @var string
     */
    protected $singular;

    /**
     * The plural label
     *
     * @var string
     */
    protected $plural;

    /**
     * The taxonomies
     *
     * @var array
     */
    protected $taxonomies = [];

    /**
     * The registered taxonomies
     *
     * @var array
     */
    protected $registeredTaxonomies = [];

    /**
     * The options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Register the post type and taxonomies
     */
    public function __construct()
    {
        $this->register();
        $this->registerTaxonomies();
    }

    /**
     * Register the post type
     *
     * @return void
     */
    protected function register()
    {
        register_post_type($this->name(), $this->options());
    }

    /**
     * Register the taxonomies
     *
     * @return void
     */
    protected function registerTaxonomies()
    {
        foreach ($this->taxonomies as $taxonomy) {
            if (class_exists($taxonomy)) {
                $this->registeredTaxonomies[$taxonomy] = new $taxonomy($this);
            } elseif ($taxonomy === 'category') {
                $this->registeredTaxonomies[$taxonomy] = new Category($this);
            } elseif ($taxonomy === 'post_tag') {
                $this->registeredTaxonomies[$taxonomy] = new PostTag($this);
            }
        }
    }

    /**
     * Get the post type name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the post type options
     *
     * @return array
     */
    public function options()
    {
        $s = $this->singular;
        $p = $this->plural;

        $labels = [
            'name'                  => __($p),
            'singular_name'         => __($s),
            'all_items'             => __("All {$p}"),
            'archives'              => __("{$s} Archives"),
            'attributes'            => __("{$s} Attributes"),
            'insert_into_item'      => __("Insert Into {$s}"),
            'uploaded_to_this_item' => __("Uploaded To This {$s}"),
            'filter_items_list'     => __("Filter {$p} List"),
            'items_list_navigation' => __("{$p} List Navigation"),
            'items_list'            => __("{$p} List"),
            'new_item'              => __("New {$s}"),
            'add_new'               => __("Add New"),
            'add_new_item'          => __("Add New {$s}"),
            'edit_item'             => __("Edit {$s}"),
            'view_item'             => __("View {$s}"),
            'view_items'            => __("View {$p}"),
            'search_items'          => __("Search {$p}"),
            'not_found'             => __("No {$p} Found"),
            'not_found_in_trash'    => __("No {$p} Found In Trash"),
            'parent_item_colon'     => __("Parent {$s}:"),
            'menu_name '            => __($p),
        ];

        return array_merge($this->options, [
            'labels' => array_merge($labels, $this->labels()),
        ]);
    }

    /**
     * Return the post type labels
     *
     * @return array
     */
    public function labels()
    {
        return [];
    }
}
