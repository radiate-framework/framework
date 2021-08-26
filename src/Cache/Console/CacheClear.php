<?php

namespace Radiate\Cache\Console;

use Radiate\Cache\Repository;
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
     * The cache instance.
     *
     * @var \Radiate\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new cache clear command instance.
     *
     * @param  \Radiate\Cache\Repository  $cache
     * @return void
     */
    public function __construct(Repository $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $successful = $this->cache->flush();

        if (!$successful) {
            return $this->error('Failed to clear cache. Make sure you have the appropriate permissions.');
        }

        $this->info('Application cache cleared!');
    }
}
