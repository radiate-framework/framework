<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;
use Radiate\Filesystem\Filesystem;

class DownCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance mode';

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
            $this->comment('Application is already down.');
        } else {
            if (
                $this->createMaintenanceTemplate() &&
                $this->createDotMaintenance()
            ) {
                $this->comment('Application is now in maintenance mode.');
            } else {
                $this->error('Failed to enter maintenance mode.');
            }
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

    /**
     * Create the maintenance template if it doesn't exist
     *
     * @return bool
     */
    protected function createMaintenanceTemplate()
    {
        if (!$this->files->exists($filePath = WP_CONTENT_DIR . '/maintenance.php')) {

            $stub = $this->files->get($this->getStub());

            $stub = str_replace('{{ site }}', get_bloginfo('name'), $stub);

            return (bool) $this->files->put($filePath, $stub);
        }
        return true;
    }

    /**
     * Get the maintenance page stub
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/maintenance.stub';
    }

    /**
     * Create the .maintenance file
     *
     * @return bool
     */
    protected function createDotMaintenance()
    {
        return (bool) $this->files->put(ABSPATH . '.maintenance', '<?php $upgrading = time(); ?>');
    }
}
