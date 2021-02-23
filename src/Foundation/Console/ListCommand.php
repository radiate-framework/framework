<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;

class ListCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the available radiate commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line("
    ____            ___       __
   / __ \____ _____/ (_)___ _/ /____
  / /_/ / __ `/ __  / / __ `/ __/ _ \
 / _, _/ /_/ / /_/ / / /_/ / /_/  __/
/_/ |_|\__,_/\__,_/_/\__,_/\__/\___/
");
        $this->call('radiate');
    }
}
