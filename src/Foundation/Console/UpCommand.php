<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;
use Radiate\Filesystem\Filesystem;

class UpCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode';

    /**
     * The filesystem
     *
     * @var \Radiate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create the command instance
     *
     * @param \Radiate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->inMaintenanceMode()) {
            if ($this->files->delete(ABSPATH . '.maintenance')) {
                $this->info('Application is now live.');
            } else {
                $this->error('Failed to disable maintenance mode.');
            }
        } else {
            $this->comment('Application is already up.');
        }
    }

    /**
     * Determine if the application is in maintenance mode.
     *
     * @return bool
     */
    protected function inMaintenanceMode()
    {
        return $this->files->exists(ABSPATH . '.maintenance');
    }
}
