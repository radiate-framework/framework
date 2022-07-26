<?php

namespace Radiate\View\Compilers\Concerns;

trait CompilesIncludes
{
    /**
     * Compile file includes
     *
     * @param string $value
     * @return string
     */
    public function compileInclude(string $value): string
    {
        $value = $this->stripParentheses($value);

        return "<?php echo \$__env->make({$value}, \Radiate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
    }
}
