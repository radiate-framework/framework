<?php

namespace Radiate\Events\Console;

use Radiate\Console\GeneratorCommand;

class MakeSubscriber extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Subscriber';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:subscriber {name : The name of the subscriber class}
                                            {--force : Overwrite the subscriber class if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new subscriber class';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/subscriber.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Listeners';
    }
}
