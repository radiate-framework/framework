<?php

namespace Radiate\WordPress;

abstract class Cpt
{
    /**
     * The post type
     *
     * @var string
     */
    protected $name;

    /**
     * The post type singular
     *
     * @var string
     */
    protected $singular;

    /**
     * The post type plural
     *
     * @var string
     */
    protected $plural;

    /**
     * Options for post type registration
     *
     * @var array
     */
    protected $options = [];

    /**
     * Register the post type
     *
     * @return static
     */
    public function register()
    {
        $p = $this->plural;
        $s = $this->singular;

        $labels = [
            'name'                  => __($p),
            'singular_name'         => __($s),
            'all_items'             => __("All {$p}"),
            'archives'              => __("{$s} Archives"),
            'attributes'            => __("{$s} Attributes"),
            'insert_into_item'      => __("Insert into {$s}"),
            'uploaded_to_this_item' => __("Uploaded to this {$s}"),
            'filter_items_list'     => __("Filter {$p} list"),
            'items_list_navigation' => __("{$p} list navigation"),
            'items_list'            => __("{$p} list"),
            'new_item'              => __("New {$s}"),
            'add_new'               => __("Add New"),
            'add_new_item'          => __("Add New {$s}"),
            'edit_item'             => __("Edit {$s}"),
            'view_item'             => __("View {$s}"),
            'view_items'            => __("View {$p}"),
            'search_items'          => __("Search {$p}"),
            'not_found'             => __("No {$p} found"),
            'not_found_in_trash'    => __("No {$p} found in trash"),
            'parent_item_colon'     => __("Parent {$s}:"),
            'menu_name '            => __($p),
        ];

        register_post_type(
            $this->name,
            array_merge(['labels' => $labels], $this->options)
        );

        return $this;
    }

    /**
     * Get the cpt name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}
