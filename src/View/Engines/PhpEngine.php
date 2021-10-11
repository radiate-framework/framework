<?php

namespace Radiate\View\Engines;

use Radiate\Filesystem\Filesystem;
use Radiate\View\Engine;

class PhpEngine implements Engine
{
    /**
     * The filesystem instance.
     *
     * @var \Radiate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new file engine instance.
     *
     * @param  \Radiate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
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
        return $this->evaluatePath($path, $data);
    }

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    protected function evaluatePath(string $path, array $data = []): string
    {
        ob_start();
        $this->files->getRequire($path, $data);
        return ltrim(ob_get_clean());
    }
}
