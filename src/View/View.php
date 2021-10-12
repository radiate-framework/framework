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

        $response = isset($callback) ? $callback($this, $contents) : null;

        return $response ?: $contents;
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
