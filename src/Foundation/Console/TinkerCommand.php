<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;

class TinkerCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interact with your application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('shell');
    }
}
