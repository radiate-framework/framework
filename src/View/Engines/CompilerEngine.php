<?php

namespace Radiate\View\Engines;

use Radiate\Filesystem\Filesystem;
use Radiate\View\Compilers\CompilerInterface;

class CompilerEngine extends PhpEngine
{
    /**
     * The Blade compiler instance.
     *
     * @var \Radiate\View\Compilers\CompilerInterface
     */
    protected $compiler;

    /**
     * Create a new file engine instance.
     *
     * @param  \Radiate\View\Compilers\CompilerInterface  $compiler
     * @param  \Radiate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(CompilerInterface $compiler, Filesystem $files)
    {
        parent::__construct($files);

        $this->compiler = $compiler;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get(string $path, array $data = []): string
    {
        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        $results = $this->evaluatePath($this->compiler->getCompiledPath($path), $data);

        array_pop($this->lastCompiled);

        return $results;
    }

    /**
     * Get the compiler implementation.
     *
     * @return \Radiate\View\Compilers\CompilerInterface
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}
