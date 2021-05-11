<?php

namespace Radiate\Cache\Console;

use Radiate\Cache\Repository;
use Radiate\Console\Command;

class CacheForget extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cache:forget {key : The key to remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an item from the cache';

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
        $this->cache->forget($this->argument('key'));

        $this->info('The [' . $this->argument('key') . '] key has been removed from the cache.');
    }
}
