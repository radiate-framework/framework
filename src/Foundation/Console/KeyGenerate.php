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
    protected $signature = 'key:generate {--show : Display the key instead of modifying files}
                                         {--force : Force the operation to run when in production}';

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
        
        if ($this->option('show')) {
            return $this->comment($key);
        }
        
        if (!$this->confirmToProceed()) {
            return;
        }
        
        $this->call('config set RADIATE_KEY', [$key, '--quiet' => true]);
        
        $this->info('Application key set successfully.');
    }
    
    /**
     * Confirm the action if in production.
     *
     * @return boolean
     */
    protected function confirmToProceed()
    {      
        if ($this->app->isProduction()) {
            if ($this->hasOption('force') && $this->option('force')) {
                return true;
            } 
            
            $this->alert('Application In Production!');

            if (!$this->confirm('Do you really wish to run this command?')) {
                $this->comment('Command Cancelled!');

                return false;
            }
        }

        return true;
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
