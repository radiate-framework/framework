<?php

namespace Radiate\View;

use Closure;
use InvalidArgumentException;

class EngineResolver
{
    /**
     * The array of engine resolvers.
     *
     * @var array
     */
    protected $resolvers = [];

    /**
     * The resolved engine instances.
     *
     * @var array
     */
    protected $resolved = [];

    /**
     * Register a new engine resolver.
     *
     * The engine string typically corresponds to a file extension.
     *
     * @param  string  $engine
     * @param  \Closure  $resolver
     * @return void
     */
    public function register($engine, Closure $resolver): void
    {
        unset($this->resolved[$engine]);

        $this->resolvers[$engine] = $resolver;
    }

    /**
     * Resolve an engine instance by name.
     *
     * @param  string  $engine
     * @return \Radiate\View\Engine
     *
     * @throws \InvalidArgumentException
     */
    public function resolve(string $engine): Engine
    {
        if (isset($this->resolved[$engine])) {
            return $this->resolved[$engine];
        }

        if (isset($this->resolvers[$engine])) {
            return $this->resolved[$engine] = call_user_func($this->resolvers[$engine]);
        }

        throw new InvalidArgumentException("Engine [{$engine}] not found.");
    }

    /**
     * Remove a resolved engine.
     *
     * @param  string  $engine
     * @return void
     */
    public function forget(string $engine): void
    {
        unset($this->resolved[$engine]);
    }
}
