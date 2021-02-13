<?php

namespace Radiate\View;

class View
{
    /**
     * The views base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Create the view instance
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Make a view
     *
     * @param string $path
     * @param array $args
     * @return string
     */
    public function make(string $path, array $args = []): string
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path) . '.php';

        ob_start();

        extract($args);

        require $this->basePath . DIRECTORY_SEPARATOR . $path;

        return ob_get_clean();
    }
}
