<?php

namespace Radiate\View\Compilers\Concerns;

trait CompilesComments
{
    /**
     * Compile comments
     *
     * @param string $value
     * @return string
     */
    public function compileComments(string $value): string
    {
        return preg_replace('/{{--(.*?)--}}/s', '', $value);
    }
}
