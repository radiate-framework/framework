<?php

namespace Radiate\View;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;

class View implements Renderable
{
    use Macroable;

    /**
     * The view.
     *
     * @var string
     */
    protected $view;

    /**
     * The view directory.
     *
     * @var string
     */
    protected $path;

    /**
     * The view data.
     *
     * @var array
     */
    protected $data;

    /**
     * Set the view directory.
     *
     * @param string $view
     * @param string $path
     * @param array $data
     */
    public function __construct(string $view, string $path, array $data = [])
    {
        $this->view = $view;
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Get the string contents of the view.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->evaluatePath($this->path, $this->data);
    }

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param string $__path
     * @param array $__data
     * @return string
     */
    protected function evaluatePath(string $__path, array $__data): string
    {
        ob_start();
        extract($__data, EXTR_SKIP);
        include $__path;
        return ltrim(ob_get_clean());
    }

    /**
     * Return the view.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
