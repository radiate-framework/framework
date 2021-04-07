<?php

namespace Radiate\Database\Console;

use Radiate\Console\GeneratorCommand;
use Radiate\Support\Str;

class MakeModel extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:model {name : The name of the model}
                                       {--user : Extend the base User model}
                                       {--term : Extend the base Term model}
                                       {--force : Overwrite the model if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class';

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

        $type = Str::snake($this->getNameInput());

        return str_replace('{{ type }}', $type, $stub);
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('term')) {
            return __DIR__ . '/stubs/model.term.stub';
        } elseif ($this->option('user')) {
            return __DIR__ . '/stubs/model.user.stub';
        } else {
            return __DIR__ . '/stubs/model.post.stub';
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
        return $rootNamespace . '\\Models';
    }
}
