<?php

namespace Radiate\Auth;

use InvalidArgumentException;
use Radiate\Foundation\Application;

class AuthManager
{
    /**
     * The application instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * The registered providers
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Create the manager instance
     *
     * @param \Radiate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the auth provider
     *
     * @param string|null $name
     * @return \Radiate\Auth\UserProvider
     */
    public function provider(?string $name = null): UserProvider
    {
        $name = $name ?: $this->getDefaultProvider();

        return $this->providers[$name] ?? $this->providers[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given guard.
     *
     * @param string $name
     * @return \Radiate\Auth\UserProvider
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve(string $name): UserProvider
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Auth provider [{$name}] is not defined.");
        }

        if (method_exists($this, $method = 'create' . ucfirst($name) . 'Provider')) {
            return $this->{$method}($config);
        }

        throw new InvalidArgumentException(
            "Auth driver [{$config['driver']}] for provider [{$name}] is not defined."
        );
    }

    /**
     * Create a Radiate user provider
     *
     * @param array $config
     * @return \Radiate\Auth\RadiateUserProvider
     */
    public function createRadiateProvider(array $config): RadiateUserProvider
    {
        return new RadiateUserProvider($config['model']);
    }

    /**
     * Create a WordPress user provider
     *
     * @return \Radiate\Auth\WordPressUserProvider
     */
    public function createWordpressProvider(): WordPressUserProvider
    {
        return new WordPressUserProvider();
    }

    /**
     * Get the provider configuration.
     *
     * @param string $name
     * @return array
     */
    protected function getConfig(string $name): array
    {
        return $this->app['config']["auth.providers.{$name}"];
    }

    /**
     * Get the default authentication provider name.
     *
     * @return string
     */
    public function getDefaultProvider(): string
    {
        return $this->app['config']['auth.default'] ?? 'radiate';
    }

    /**
     * Dynamically call the auth provider
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->provider()->$method(...$parameters);
    }
}
