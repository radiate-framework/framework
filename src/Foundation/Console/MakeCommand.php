<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\GeneratorCommand;

class MakeCommand extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Command';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:command {name : The name of the command}
                                         {--command=command:name : The terminal command that should be assigned}
                                         {--force : Overwrite the command if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new Radiate command';

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('{{ command }}', $this->option('command'), $stub);
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/command.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Console';
    }
}
