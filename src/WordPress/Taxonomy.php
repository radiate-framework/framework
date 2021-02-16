<?php

namespace Radiate\WordPress;

abstract class Taxonomy
{
    /**
     * The taxonomy name
     *
     * @var string
     */
    protected $name;

    /**
     * The taxonomy singular
     *
     * @var string
     */
    protected $singular;

    /**
     * The taxonomy plural
     *
     * @var string
     */
    protected $plural;

    /**
     * The post type
     *
     * @var \Radiate\WordPress\Cpt
     */
    protected $cpt;

    /**
     * Options for taxonomy registration
     *
     * @var array
     */
    protected $options = [];

    /**
     * Register the post type
     *
     * @param string $cpt
     */
    public function __construct(Cpt $cpt)
    {
        $this->cpt = $cpt;
    }

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
            'separate_items_with_commas' => __("Separate {$p} with commas"),
            'add_or_remove_items'        => __("Add or remove {$p}"),
            'choose_from_most_used'      => __("Choose from the most used {$p}"),
            'not_found'                  => __("No {$p} found"),
            'no_terms'                   => __("No {$p}"),
            'items_list_navigation'      => __("{$p} list navigation"),
            'items_list'                 => __("{$p} list"),
            'most_used'                  => __('Most Used'),
            'back_to_items'              => __("&larr; Go to {$p}"),
        ];

        if (taxonomy_exists($this->name)) {
            register_taxonomy_for_object_type($this->name, $this->cpt->name());
        } else {
            register_taxonomy(
                $this->name,
                $this->cpt->name(),
                array_merge(['labels' => $labels], $this->options)
            );
        }

        return $this;
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
}
