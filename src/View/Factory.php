<?php

namespace Radiate\View;

use Closure;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Radiate\Support\Arr;
use Radiate\Support\Markdown;
use Radiate\Support\Str;
use Radiate\View\Concerns\ManagesLayouts;

class Factory
{
    use Macroable;
    use ManagesLayouts;

    /**
     * The engine resolver
     *
     * @var \Radiate\View\EngineResolver
     */
    protected $engines;

    /**
     * The file finder
     *
     * @var \Radiate\View\Finder
     */
    protected $finder;

    /**
     * The extension to engine bindings.
     *
     * @var array
     */
    protected $extensions = [
        'blade.php' => 'blade',
        'php'       => 'php',
        'html'      => 'file',
        'css'       => 'file',
    ];

    /**
     * Data that should be available to all templates.
     *
     * @var array
     */
    protected $shared = [];

    /**
     * Create the factory
     *
     * @param \Radiate\View\EngineResolver $engines
     * @param \Radiate\View\Finder $finder
     */
    public function __construct(EngineResolver $engines, Finder $finder)
    {
        $this->engines = $engines;
        $this->finder = $finder;

        $this->share('__env', $this);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return \Radiate\View\View
     */
    public function make(string $view, array $data = [], array $mergeData = []): View
    {
        $path = $this->finder->find(
            $view = $this->normalizeName($view)
        );

        return $this->viewInstance($view, $path, $data, $mergeData);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $path
     * @param  array  $data
     * @param array $mergeData
     * @return \Radiate\View\View
     */
    public function file(string $path, array $data = [], array $mergeData = []): View
    {
        return $this->viewInstance($path, $path, $data, $mergeData);
    }

    /**
     * Get the markdown contents for the given view.
     *
     * @param  string  $path
     * @param  array  $data
     * @return \Radiate\Support\Markdown
     */
    public function markdown(string $view, array $data = [], array $mergeData = []): Markdown
    {
        return Str::markdown($this->make($view, $data,  $mergeData));
    }

    /**
     * Create a new view instance from the given arguments.
     *
     * @param string $view
     * @param string $path
     * @param array $data
     * @param array $mergeData
     * @return \Radiate\View\View
     */
    protected function viewInstance(string $view, string $path, array $data = [], array $mergeData = []): View
    {
        $data = array_merge($mergeData, $data);

        return new View($this, $this->getEngineFromPath($path), $view, $path, $data);
    }

    /**
     * Normalize the name (replace dot notation for slashes).
     *
     * @param string $path
     * @return string
     */
    protected function normalizeName(string $path): string
    {
        return str_replace('.', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Get the appropriate view engine for the given path.
     *
     * @param  string  $path
     * @return \Radiate\View\Engine
     *
     * @throws \InvalidArgumentException
     */
    public function getEngineFromPath(string $path): Engine
    {
        if (!$extension = $this->getExtension($path)) {
            throw new InvalidArgumentException("Unrecognized extension in file: {$path}.");
        }

        $engine = $this->extensions[$extension];

        return $this->engines->resolve($engine);
    }

    /**
     * Register a valid view extension and its engine.
     *
     * @param  string  $extension
     * @param  string  $engine
     * @param  \Closure|null  $resolver
     * @return void
     */
    public function addExtension(string $extension, string $engine, ?Closure $resolver = null): void
    {
        $this->finder->addExtension($extension);

        if (isset($resolver)) {
            $this->engines->register($engine, $resolver);
        }

        unset($this->extensions[$extension]);

        $this->extensions = array_merge([$extension => $engine], $this->extensions);
    }

    /**
     * Get the extension used by the view file.
     *
     * @param  string  $path
     * @return string|null
     */
    protected function getExtension(string $path): ?string
    {
        $extensions = array_keys($this->extensions);

        return Arr::first($extensions, function ($value) use ($path) {
            return Str::endsWith($path, '.' . $value);
        });
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed|null  $value
     * @return mixed
     */
    public function share($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            $this->shared[$key] = $value;
        }

        return $value;
    }

    /**
     * Get an item from the shared data.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function shared(string $key, $default = null)
    {
        return $this->shared[$key] ?? $default;
    }

    /**
     * Get all of the shared data for the environment.
     *
     * @return array
     */
    public function getShared(): array
    {
        return $this->shared;
    }

    /**
     * Determine if a given view exists.
     *
     * @param  string  $view
     * @return bool
     */
    public function exists(string $view): bool
    {
        try {
            $this->finder->find($view);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }
}
