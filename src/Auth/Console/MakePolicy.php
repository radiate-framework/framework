<?php

namespace Radiate\Auth\Console;

use LogicException;
use Radiate\Console\GeneratorCommand;

class MakePolicy extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Policy';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:policy {name : The name of the policy}
                                        {--force : Overwrite the policy if it exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new policy class';

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

        $userModel = $this->userProviderModel();

        return str_replace('{{ userModel }}', $userModel, $stub);
    }

    /**
     * Get the model for the guard's user provider.
     *
     * @return string|null
     */
    protected function userProviderModel()
    {
        $config = $this->app['config'];

        $guard = $this->option('guard') ?: $config->get('auth.defaults.guard');

        if (is_null($guardProvider = $config->get('auth.guards.' . $guard . '.provider'))) {
            throw new LogicException('The [' . $guard . '] guard is not defined in your "auth" configuration file.');
        }

        return $config->get(
            'auth.providers.' . $guardProvider . '.model'
        );
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/policy.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Policies';
    }
}
