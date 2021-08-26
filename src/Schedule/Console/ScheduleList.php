<?php

namespace Radiate\Schedule\Console;

use Radiate\Console\Command;

class ScheduleList extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'schedule:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the scheduled commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('cron event list');
    }
}
