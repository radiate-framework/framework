<?php

namespace Radiate\WordPress\Console;

use Radiate\Console\GeneratorCommand;
use Radiate\Support\Facades\Str;

class MakeTaxonomy extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Taxonomy';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:taxonomy {name : The name of the taxonomy}
                                          {--force : Overwrite the taxonomy if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a taxonomy';

    /**
     * Reserved taxonomies that cannot be used for generation.
     *
     * @var array
     */
    protected $reservedTaxonomies = [
        'action',
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'calendar',
        'cat',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_per_page',
        'comments_popup',
        'custom',
        'customize_messenger_channel',
        'customized',
        'cpage',
        'day',
        'debug',
        'embed',
        'error',
        'exact',
        'feed',
        'fields',
        'hour',
        'link_category',
        'm',
        'minute',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nonce',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'page_id',
        'paged',
        'pagename',
        'pb',
        'perm',
        'post',
        'post__in',
        'post__not_in',
        'post_format',
        'post_mime_type',
        'post_status',
        'post_type',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'status',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'terms',
        'theme',
        'title',
        'type',
        'types',
        'w',
        'withcomments',
        'withoutcomments',
        'year',
    ];

    /**
     * Reserved taxonomies that cannot be used for generation.
     *
     * @var array
     */
    protected $specialTaxonomies = [
        'category',
        'post_tag',
    ];

    /**
     * Call the parent handler and then flush rewrite rules
     *
     * @return void
     */
    protected function handle()
    {
        // First we need to ensure that the given name is not a reserved word within WordPress.
        if ($this->isReservedTaxonomy($name = Str::snake($this->getNameInput()))) {
            $this->error('The name "' . $name . '" is reserved by WordPress.');

            return;
        }

        parent::handle();

        flush_rewrite_rules();
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isReservedTaxonomy(string $name)
    {
        return in_array($name, $this->reservedTaxonomies);
    }

    /**
     * Checks whether the given name is a pre-registered taxonomy.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isSpecialTaxonomy(string $name)
    {
        return in_array($name, $this->specialTaxonomies);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        $name = Str::snake($this->getNameInput());
        $singular = (string) Str::of($name)->replace('_', ' ')->title;
        $plural = Str::plural($singular);

        return str_replace(
            ['{{ name }}', '{{ singular }}', '{{ plural }}'],
            [$name, $singular, $plural],
            $stub
        );
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->isSpecialTaxonomy(Str::snake($this->getNameInput()))) {
            return __DIR__ . '/stubs/taxonomy.reserved.stub';
        }

        return __DIR__ . '/stubs/taxonomy.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Cpts\\Taxonomies';
    }
}
