<?php

namespace Radiate\View;

class Factory
{
    /**
     * The file finder
     *
     * @var \Radiate\View\Finder
     */
    protected $finder;

    /**
     * Set the view directory.
     *
     * @param string $path
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Make a view.
     *
     * @param string $view
     * @param array $data
     * @return \Radiate\View\View
     */
    public function make(string $view, array $data = [])
    {
        $path = $this->finder->find(
            $view = $this->normalizeName($view)
        );

        return $this->viewInstance($view, $path, $data);
    }

    /**
     * Create a new view instance from the given arguments.
     *
     * @param string $view
     * @param string $path
     * @param array $data
     * @return \Radiate\View\View
     */
    protected function viewInstance(string $view, string $path, array $data = []): View
    {
        return new View($view, $path, $data);
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
}
