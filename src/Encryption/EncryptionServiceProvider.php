<?php

namespace Radiate\Encryption;

use Radiate\Support\ServiceProvider;
use Radiate\Support\Str;
use RuntimeException;

class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('encrypter', function ($app) {
            $config = $app['config']['app'];

            return new Encrypter($this->parseKey($config), $config['cipher']);
        });
    }

    /**
     * Parse the encryption key.
     *
     * @param  array  $config
     * @return string
     */
    protected function parseKey(array $config)
    {
        if (Str::startsWith($key = $this->key($config), $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }

    /**
     * Extract the encryption key from the given configuration.
     *
     * @param  array  $config
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function key(array $config)
    {
        if (empty($config['key'])) {
            throw new MissingAppKeyException();
        }

        return $config['key'];
    }
}
