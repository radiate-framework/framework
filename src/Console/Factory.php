<?php

namespace Radiate\Console;

use Radiate\Foundation\Application;

class Factory
{
    /**
     * Create the factory instance
     *
     * @param \Radiate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Make a Command
     *
     * @param string $command
     * @return void
     */
    public function make(string $command)
    {
        $this->app->make($command)->setContainer($this->app)->register();
    }
}
