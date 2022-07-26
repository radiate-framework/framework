<?php

namespace Radiate\View\Compilers;

use Radiate\Support\Arr;
use Radiate\Support\Str;
use Radiate\View\Compilers\Concerns\CompilesComments;
use Radiate\View\Compilers\Concerns\CompilesEchos;
use Radiate\View\Compilers\Concerns\CompilesIncludes;

class BladeCompiler extends Compiler implements CompilerInterface
{
    use CompilesComments;
    use CompilesEchos;
    use CompilesIncludes;


    protected array $compilers = [
        'Comments',
        'Statements',
        'Echos',
    ];

    protected array $footer = [];

    public function compile(string $path): void
    {
        if (!is_null($this->cachePath)) {
            $contents = $this->compileString($this->files->get($path));

            if (!empty($path)) {
                $contents = $this->appendFilePath($contents, $path);
            }

            $this->ensureCompiledDirectoryExists(
                $compiledPath = $this->getCompiledPath($path)
            );

            $this->files->put($compiledPath, $contents);
        }
    }

    public function compileString($value)
    {
        $this->footer = [];

        foreach ($this->compilers as $type) {
            $value = $this->{"compile{$type}"}($value);
        }

        if (count($this->footer) > 0) {
            $value = $this->addFooters($value);
        }

        return $value;
    }

    protected function compileStatements($value)
    {
        return preg_replace_callback(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            function ($match) {
                return $this->compileStatement($match);
            },
            $value
        );
    }

    protected function compileStatement($match)
    {
        if (Str::contains($match[1], '@')) {
            $match[0] = isset($match[3]) ? $match[1] . $match[3] : $match[1];
        } elseif (isset($this->customDirectives[$match[1]])) {
            $match[0] = $this->callCustomDirective($match[1], Arr::get($match, 3));
        } elseif (method_exists($this, $method = 'compile' . ucfirst($match[1]))) {
            $match[0] = $this->$method(Arr::get($match, 3));
        }

        return isset($match[3]) ? $match[0] : $match[0] . $match[2];
    }

    protected function callCustomDirective($name, $value)
    {
        $value = $value ?? '';

        if (Str::startsWith($value, '(') && Str::endsWith($value, ')')) {
            $value = Str::substr($value, 1, -1);
        }

        return call_user_func($this->customDirectives[$name], trim($value));
    }

    protected function appendFilePath(string $contents, string $path): string
    {
        return $contents . "<?php /**PATH {$path} ENDPATH**/ ?>";
    }

    protected function addFooters($result)
    {
        return ltrim($result, "\n")
            . "\n" . implode("\n", array_reverse($this->footer));
    }

    public function stripParentheses($expression)
    {
        if (Str::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        return $expression;
    }
}
