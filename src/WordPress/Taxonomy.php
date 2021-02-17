<?php

namespace Radiate\WordPress;

abstract class Taxonomy
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
     * The options
     *
     * @var array
     */
    protected $options = [];

    /**
     * The post type
     *
     * @var \Radiate\WordPress\Cpt
     */
    protected $postType;

    /**
     * Register the taxonomy
     *
     * @var \Radiate\WordPress\Cpt
     */
    public function __construct(Cpt $postType)
    {
        $this->postType = $postType;

        $this->register();
    }

    /**
     * Register the taxonomy
     *
     * @return void
     */
    protected function register()
    {
        if ($this->exists()) {
            register_taxonomy_for_object_type(
                $this->name(),
                $this->postType->name()
            );
        } else {
            register_taxonomy(
                $this->name(),
                $this->postType->name(),
                $this->options()
            );
        }
    }

    /**
     * Determine if the taxonomy already exists
     *
     * @return bool
     */
    protected function exists()
    {
        return taxonomy_exists($this->name());
    }

    /**
     * Get the taxonomy name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the taxonomy options
     *
     * @return array
     */
    public function options()
    {
        $s = $this->singular;
        $p = $this->plural;

        $labels = [
            'name'                       => __($p),
            'singular_name'              => __($s),
            'search_items'               => __("Search {$p}"),
            'popular_items'              => __("Popular {$p}"),
            'all_items'                  => __("All {$p}"),
            'parent_item'                => __("Parent {$s}"),
            'parent_item_colon'          => __("Parent {$s}:"),
            'edit_item'                  => __("Edit {$s}"),
            'view_item'                  => __("View {$s}"),
            'update_item'                => __("Update {$s}"),
            'add_new_item'               => __("Add New {$s}"),
            'new_item_name'              => __("New {$s} Name"),
            'separate_items_with_commas' => __("Separate {$p} With Commas"),
            'add_or_remove_items'        => __("Add Or Remove {$p}"),
            'choose_from_most_used'      => __("Choose From The Most Used {$p}"),
            'not_found'                  => __("No {$p} Found"),
            'no_terms'                   => __("No {$p}"),
            'items_list_navigation'      => __("{$p} List Navigation"),
            'items_list'                 => __("{$p} List"),
            'most_used'                  => __('Most Used'),
            'back_to_items'              => __("&larr; Go To {$p}"),
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
