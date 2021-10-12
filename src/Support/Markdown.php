<?php

namespace Radiate\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Parsedown;
use Stringable;

class Markdown extends Parsedown implements Htmlable, Renderable, Stringable
{
    /**
     * The original content
     *
     * @var string
     */
    protected $original;

    /**
     * Create the markdown instance
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->original = $content;
    }

    /**
     * Return the original content
     *
     * @return string
     */
    public function original(): string
    {
        return $this->original;
    }

    /**
     * Render the markdown
     *
     * @return string
     */
    public function render(): string
    {
        return $this->text($this->original);
    }

    /**
     * Convert the markdown
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->render();
    }

    /**
     * Convert to a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
