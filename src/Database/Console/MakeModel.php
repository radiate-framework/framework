<?php

namespace Radiate\Database\Console;

use Radiate\Console\GeneratorCommand;

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
                                       {--type=post : The type of model to create - post, term, user}
                                       {--force : Overwrite the model if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a model';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('type') == 'term') {
            return __DIR__ . '/stubs/model.term.stub';
        } elseif ($this->option('type') == 'user') {
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
