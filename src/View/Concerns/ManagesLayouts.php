<?php

namespace Radiate\View\Concerns;

use InvalidArgumentException;
use Radiate\View\View;

trait ManagesLayouts
{
    /**
     * All of the finished, captured sections.
     *
     * @var array
     */
    protected array $sections = [];

    /**
     * The stack of in-progress sections.
     *
     * @var array
     */
    protected array $sectionStack = [];

    /**
     * Start injecting content into a section.
     *
     * @param  string  $section
     * @param  string|null  $content
     * @return void
     */
    public function startSection(string $section, ?string $content = null): void
    {
        if ($content === null) {
            if (ob_start()) {
                $this->sectionStack[] = $section;
            }
        } else {
            $this->extendSection($section, $content instanceof View ? $content : esc_html($content));
        }
    }

    /**
     * Stop injecting content into a section.
     *
     * @param  bool  $overwrite
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function stopSection(bool $overwrite = false): string
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot end a section without first starting one.');
        }

        $last = array_pop($this->sectionStack);

        if ($overwrite) {
            $this->sections[$last] = ob_get_clean();
        } else {
            $this->extendSection($last, ob_get_clean());
        }

        return $last;
    }

    /**
     * Get the string contents of a section.
     *
     * @param  string  $section
     * @param  string  $default
     * @return string
     */
    public function yieldContent(string $section, string $default = ''): string
    {
        $sectionContent = $default instanceof View ? $default : esc_html($default);

        if (isset($this->sections[$section])) {
            $sectionContent = $this->sections[$section];
        }

        return $sectionContent;
    }

    /**
     * Append content to a given section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendSection(string $section, string $content): void
    {
        $this->sections[$section] = $content;
    }

    /**
     * Check if the section exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasSection(string $name): bool
    {
        return array_key_exists($name, $this->sections);
    }
}
