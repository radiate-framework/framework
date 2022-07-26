<?php

namespace Radiate\View\Compilers\Concerns;

trait CompilesLayout
{
    /**
     * Start a section
     *
     * @param string $value
     * @return string
     */
    public function compileSection(string $value): string
    {
        return "<?php \$__env->startSection{$value}; ?>";
    }

    /**
     * End a section
     *
     * @param string $value
     * @return string
     */
    public function compileEndsection(): string
    {
        return "<?php \$__env->stopSection(); ?>";
    }

    /**
     * echo a section
     *
     * @param string $value
     * @return string
     */
    public function compileYield(string $value): string
    {
        return "<?php echo \$__env->yieldContent{$value}; ?>";
    }

    /**
     * Extend a template
     *
     * @param string $value
     * @return string
     */
    public function compileExtends(string $value): string
    {
        $value = $this->stripParentheses($value);

        $echo = "<?php echo \$__env->make({$value}, \Radiate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";

        $this->footer[] = $echo;

        return '';
    }
}
