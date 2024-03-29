<?php

namespace Radiate\View\Engines;

use Radiate\Filesystem\Filesystem;
use Radiate\View\Engine;

class FileEngine implements Engine
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
        return $this->files->get($path);
    }
}
