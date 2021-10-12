<?php

namespace Radiate\View;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;
use Stringable;

class View implements Htmlable, Renderable, Stringable
{
    use Macroable;

    /**
     * The factory.
     *
     * @var \Radiate\View\Factory
     */
    protected $factory;

    /**
     * The engine.
     *
     * @var \Radiate\View\Engine
     */
    protected $engine;

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
     * @param \Radiate\View\Factory $factory
     * @param \Radiate\View\Engine $engine
     * @param string $view
     * @param string $path
     * @param array $data
     */
    public function __construct(Factory $factory, Engine $engine, string $view, string $path, array $data = [])
    {
        $this->factory = $factory;
        $this->engine = $engine;
        $this->view = $view;
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Get the string contents of the view.
     *
     * @param callable|null $callback
     * @return string
     */
    public function render(?callable $callback = null): string
    {
        $contents = $this->getContents();

        return !is_null($callback) ? $callback($this, $contents) : $contents;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    protected function getContents(): string
    {
        return $this->engine->get($this->path, $this->gatherData());
    }

    /**
     * Get the data bound to the view instance.
     *
     * @return array
     */
    public function gatherData(): array
    {
        $data = array_merge($this->factory->getShared(), $this->data);

        foreach ($data as $key => $value) {
            if ($value instanceof Renderable) {
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->view;
    }

    /**
     * Get the array of view data.
     *
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Get the path to the view file.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the view factory instance.
     *
     * @return \Radiate\View\Factory
     */
    public function factory(): Factory
    {
        return $this->factory;
    }

    /**
     * Get the view's rendering engine.
     *
     * @return \Radiate\View\Engine
     */
    public function engine(): Engine
    {
        return $this->engine;
    }

    /**
     * Return the view.
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->render();
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
