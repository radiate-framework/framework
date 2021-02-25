<?php

namespace Radiate\WordPress\Console;

use Radiate\Console\GeneratorCommand;
use Radiate\Support\Str;

class MakeShortcode extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Shortcode';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:shortcode {name : The name of the shortcode class}
                                           {--force : Overwrite the shortcode class if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a new shortcode class';

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

        return str_replace('{{ name }}', $name, $stub);
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/shortcode.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\WordPress\\Shortcodes';
    }
}
