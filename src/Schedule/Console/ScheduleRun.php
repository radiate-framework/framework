<?php

namespace Radiate\Schedule\Console;

use Radiate\Console\Command;

class ScheduleRun extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'schedule:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduled commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('cron event run --all');
    }
}
