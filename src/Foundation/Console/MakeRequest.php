<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\GeneratorCommand;

class MakeRequest extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:request {name : The name of the form request}
                                         {--force : Overwrite the form request if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request class';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/request.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Http\\Requests';
    }
}
