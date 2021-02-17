<?php

namespace Radiate\WordPress\Console;

use Radiate\Console\GeneratorCommand;
use Radiate\Support\Facades\Str;

class MakeCpt extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Cpt';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:cpt {name : The name of the custom post type}
                                     {--force : Overwrite the cpt if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a custom post type';

    /**
     * Reserved post types that cannot be used for generation.
     *
     * @var array
     */
    protected $reservedPostTypes = [
        'revision',
        'nav_menu_item',
        'custom_css',
        'customize_changeset',
        'oembed_cache',
        'user_request',
        'wp_block',
        'action',
        'author',
        'order',
        'theme',
    ];

    /**
     * Special post types have a trimmed down class
     *
     * @var array
     */
    protected $specialPostTypes = [
        'post',
        'page',
        'attachment',
    ];

    /**
     * Call the parent handler and then flush rewrite rules
     *
     * @return void
     */
    protected function handle()
    {
        if ($this->isReservedPostType($name = Str::snake($this->getNameInput()))) {
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
    protected function isReservedPostType(string $name)
    {
        return in_array($name, $this->reservedPostTypes);
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isSpecialPostType(string $name)
    {
        return in_array($name, $this->specialPostTypes);
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
        if ($this->isSpecialPostType(Str::snake($this->getNameInput()))) {
            return __DIR__ . '/stubs/cpt.reserved.stub';
        }

        return __DIR__ . '/stubs/cpt.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Cpts';
    }
}
