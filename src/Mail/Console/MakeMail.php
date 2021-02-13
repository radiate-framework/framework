<?php

namespace Radiate\Mail\Console;

use Radiate\Console\GeneratorCommand;

class MakeMail extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Mailable';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:mail {name : The name of the mailable class}
                                      {--force : Overwrite the mailable class if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a mailable class';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/mail.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Mail';
    }
}
