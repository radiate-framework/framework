<?php

namespace Radiate\Routing\Console;

use Radiate\Console\GeneratorCommand;

class MakeController extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:controller {name : The name of the controller}
                                            {--resource : Create a resource controller}
                                            {--api : Exclude the create and edit methods from the resource controller}
                                            {--force : Overwrite the controller if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a controller';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('resource')) {
            return __DIR__ . '/stubs/controller.resource.stub';
        } elseif ($this->option('api')) {
            return __DIR__ . '/stubs/controller.api.stub';
        } else {
            return __DIR__ . '/stubs/controller.stub';
        }
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Http\\Controllers';
    }
}
