<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;
use Radiate\Encryption\Encrypter;

class KeyGenerate extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the application key';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        $this->comment($key);
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:' . base64_encode(
            Encrypter::generateKey($this->app['config']['app.cipher'])
        );
    }
}
