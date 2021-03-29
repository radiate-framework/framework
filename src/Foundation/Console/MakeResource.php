<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\GeneratorCommand;
use Radiate\Support\Str;

class MakeResource extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:resource {name : The name of the resource class}
                                          {--collection : Create a resource collection}
                                          {--force : Overwrite the resource class if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource';

    /**
     * Determine if the command is generating a resource collection.
     *
     * @return bool
     */
    protected function collection()
    {
        return $this->option('collection') ||
            Str::endsWith($this->argument('name'), 'Collection');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->collection()) {
            $this->type = 'Resource collection';
        }

        parent::handle();
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->collection()
            ? __DIR__ . '/stubs/resource-collection.stub'
            : __DIR__ . '/stubs/resource.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace . '\\Http\\Resources';
    }
}
