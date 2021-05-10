<?php

namespace Radiate\Cache\Console;

use Radiate\Console\Command;

class CacheClear extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush the application cache';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('transient delete --expired');
    }
}
