<?php

namespace Radiate\Auth;

use InvalidArgumentException;

trait CreatesUserProviders
{
    /**
     * The registered custom provider creators.
     *
     * @var array
     */
    protected $customProviderCreators = [];

    /**
     * Create the user provider implementation for the driver.
     *
     * @param  string|null  $provider
     * @return \Radiate\Auth\Contracts\UserProvider|null
     *
     * @throws \InvalidArgumentException
     */
    public function createUserProvider($provider = null)
    {
        if (is_null($config = $this->getProviderConfiguration($provider))) {
            return;
        }

        if (isset($this->customProviderCreators[$driver = ($config['driver'] ?? null)])) {
            return call_user_func(
                $this->customProviderCreators[$driver],
                $this->app,
                $config
            );
        }

        switch ($driver) {
            case 'wordpress':
                return $this->createWordPressProvider();
            case 'radiate':
                return $this->createRadiateProvider($config);
            default:
                throw new InvalidArgumentException(
                    "Authentication user provider [{$driver}] is not defined."
                );
        }
    }

    /**
     * Get the user provider configuration.
     *
     * @param  string|null  $provider
     * @return array|null
     */
    protected function getProviderConfiguration($provider)
    {
        if ($provider = $provider ?: $this->getDefaultUserProvider()) {
            return $this->app['config']['auth.providers.' . $provider];
        }
    }

    /**
     * Create an instance of the WordPress user provider.
     *
     * @return \Radiate\Auth\WordPressUserProvider
     */
    protected function createWordPressProvider()
    {
        return new WordPressUserProvider();
    }

    /**
     * Create an instance of the Radiate user provider.
     *
     * @param  array  $config
     * @return \Radiate\Auth\RadiateUserProvider
     */
    protected function createRadiateProvider($config)
    {
        return new RadiateUserProvider($config['model']);
    }

    /**
     * Get the default user provider name.
     *
     * @return string
     */
    public function getDefaultUserProvider()
    {
        return $this->app['config']['auth.defaults.provider'];
    }
}
