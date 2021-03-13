<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\GeneratorCommand;

class MakeResource extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:resource {name : The name of the resource class}
                                          {--force : Overwrite the resource class if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource class';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/resource.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Http\\Resources';
    }
}
