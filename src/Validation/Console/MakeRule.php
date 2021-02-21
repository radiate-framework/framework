<?php

namespace Radiate\Validation\Console;

use Radiate\Console\GeneratorCommand;

class MakeRule extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Rule';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:rule {name : The name of the rule}
                                      {--force : Overwrite the rule if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a rule';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/rule.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Rules';
    }
}
