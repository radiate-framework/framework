<?php

namespace Radiate\View;

use InvalidArgumentException;
use Radiate\Filesystem\Filesystem;

class Finder
{
    /**
     * The filesystem
     *
     * @var \Radiate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The view paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * The views.
     *
     * @var array
     */
    protected $views = [];

    /**
     * Register a view extension with the finder.
     *
     * @var array
     */
    protected $extensions = ['blade.php', 'php', 'html', 'css'];

    /**
     * Create the finder instance
     *
     * @param \Radiate\Filesystem\Filesystem $files
     * @param string|array $paths
     */
    public function __construct(Filesystem $files, $paths)
    {
        $this->files = $files;
        $this->paths = (array) $paths;
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param string $name
     * @return string
     */
    public function find(string $name): string
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param string $name
     * @param array $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findInPaths(string $name, array $paths): string
    {
        foreach ($paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                if ($this->files->exists($viewPath = $path . DIRECTORY_SEPARATOR . $file)) {
                    return $viewPath;
                }
            }
        }

        throw new InvalidArgumentException("View [{$name}] not found.");
    }

    /**
     * Get an array of possible view files.
     *
     * @param string $name
     * @return array
     */
    protected function getPossibleViewFiles(string $name): array
    {
        return array_map(function ($extension) use ($name) {
            return str_replace('.', DIRECTORY_SEPARATOR, $name) . '.' . $extension;
        }, $this->extensions);
    }

    /**
     * Get registered extensions.
     *
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Register an extension with the finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension(string $extension): void
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }
}
