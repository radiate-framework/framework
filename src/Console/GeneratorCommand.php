<?php

namespace Radiate\Console;

use Radiate\Foundation\Application;
use Radiate\Filesystem\Filesystem;

abstract class GeneratorCommand extends Command
{
    /**
     * The filesystem instance
     *
     * @var \Radiate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Reserved names that cannot be used for generation.
     *
     * @var array
     */
    protected $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
    ];

    /**
     * Assign the filesystem and call the parent contructor.
     *
     * @param \Radiate\Foundation\Application $app
     * @param \Radiate\Filesystem\Filesystem $files
     */
    public function __construct(Application $app, Filesystem $files)
    {
        $this->files = $files;

        parent::__construct($app);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub();

    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');

            return;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if (!$this->option('force') && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type . ' created successfully.');
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass(string $name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (strpos($name, $rootNamespace) === 0) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists(string $rawName)
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath(string $name)
    {
        $position = strpos($name, $this->rootNamespace());

        if ($position !== false) {
            $name = substr_replace($name, '', $position, strlen($this->rootNamespace()));
        }

        return $this->app->basePath('app/' . str_replace('\\', '/', $name) . '.php');
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory(string $path)
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0777, true, true);
        }
        return $path;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass(string $name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return self
     */
    protected function replaceNamespace(string &$stub, string $name)
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ rootNamespace }}'],
            [$this->getNamespace($name), $this->rootNamespace()],
            $stub
        );

        return $this;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace(string $name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass(string $stub, string $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('{{ class }}', $class, $stub);
    }

    /**
     * Alphabetically sorts the imports for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function sortImports(string $stub)
    {
        if (preg_match('/(?P<imports>(?:use [^;]+;$\n?)+)/m', $stub, $match)) {
            $imports = explode("\n", trim($match['imports']));

            sort($imports);

            return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->app->getNamespace();
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isReservedName(string $name)
    {
        $name = strtolower($name);

        return in_array($name, $this->reservedNames);
    }
}
