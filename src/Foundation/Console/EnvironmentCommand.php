<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;

class EnvironmentCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the current framework environment';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line("<info>Current application environment:</info> <comment>{$this->app['env']}</comment>");
    }
}
