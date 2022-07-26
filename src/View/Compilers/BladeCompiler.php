<?php

namespace Radiate\View\Compliers;

class BladeCompiler extends Compiler implements CompilerInterface
{
    public function compile(string $path): void
    {
        if (!is_null($this->cachePath)) {
            $contents = $this->files->get($path);

            $this->ensureCompiledDirectoryExists(
                $compiledPath = $this->getCompiledPath($path)
            );

            $this->files->put($compiledPath, $contents);
        }
    }
}
