<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\GeneratorCommand;

class MakeProvider extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Provider';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:provider {name : The name of the provider}
                                          {--force : Overwrite the provider if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider class';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/provider.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Providers';
    }
}
