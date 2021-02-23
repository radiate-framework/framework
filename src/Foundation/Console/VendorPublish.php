<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;
use Radiate\Filesystem\Filesystem;
use Radiate\Support\ServiceProvider;

class VendorPublish extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Radiate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'vendor:publish {--provider : The service provider that has assets you want to publish}
                                           {--tag : The tag to publish}
                                           {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish any publishable assets from vendor packages';

    /**
     * Assign the filesystem and call the parent contructor.
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
        $paths = ServiceProvider::pathsToPublish($this->option('provider'), $this->option('tag'));

        foreach ($paths as $from => $to) {
            if (!$to) {
                continue;
            }

            if ($this->files->copyDirectory($from, $to)) {
                $this->status($from, $to, 'Directory');
            }

            if (!$this->files->exists($to) || $this->option('force')) {
                $this->files->ensureDirectoryExists(dirname($to));

                $this->files->copy($from, $to);

                $this->status($from, $to, 'File');
            }
        }

        $this->info('Publishing complete.');
    }

    /**
     * Write a status message to the console.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @return void
     */
    protected function status(string $from, string $to, string $type): void
    {
        $from = str_replace($this->app->basePath(), '', realpath($from));

        $to = str_replace($this->app->basePath(), '', realpath($to));

        $this->line("Copied {$type} [{$from}] to [{$to}]");
    }
}
