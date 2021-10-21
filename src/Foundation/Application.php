<?php

namespace Radiate\Foundation;

use Illuminate\Container\Container;
use Radiate\Config\Repository;
use Radiate\Events\EventServiceProvider;
use Radiate\Foundation\Exceptions\Handler as ExceptionHandler;
use Radiate\Foundation\Providers\ConsoleServiceProvider;
use Radiate\Filesystem\FilesystemServiceProvider;
use Radiate\Http\Request;
use Radiate\Support\Facades\Facade;
use Radiate\Support\Pipeline;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Throwable;

class Application extends Container
{
    /**
     * The base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * The registered providers
     *
     * @var array
     */
    protected $providers = [];

    /**
     * The application namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * The global middleware
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The route middleware
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * The registered class aliases
     *
     * @var array
     */
    protected $registeredClassAliases = [];

    /**
     * Create the applicaiton
     *
     * @param string $basePath
     */
    public function __construct(string $basePath = null)
    {
        if ($basePath) {
            $this->basePath = $basePath;
        }

        $this->registerBaseBindings();
        $this->registerCoreProviders();
        $this->registerContainerAliases();
        $this->setFacadeRoot();
        $this->loadConfigurationFiles();
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);
        $this->instance(self::class, $this);

        $this->instance('env', wp_get_environment_type());

        $this->singleton(ExceptionHandler::class, function ($app) {
            return new ExceptionHandler($app);
        });
    }

    /**
     * Register the core service providers
     *
     * @return void
     */
    protected function registerCoreProviders()
    {
        $this->register(EventServiceProvider::class);
        $this->register(FilesystemServiceProvider::class);

        if ($this->runningInConsole()) {
            $this->register(ConsoleServiceProvider::class);
        }
    }

    /**
     * Register the container aliases
     *
     * @return void
     */
    protected function registerContainerAliases()
    {
        foreach ([
            'app' => [
                self::class,
                \Illuminate\Container\Container::class,
                \Illuminate\Contracts\Container\Container::class,
                \Psr\Container\ContainerInterface::class,
            ],
            'db' => [\Radiate\Database\DatabaseManager::class],
            'db.connection' => [
                \Radiate\Database\Connection::class,
                \wpdb::class,
            ],
            'auth' => [
                \Radiate\Auth\AuthManager::class,
                Illuminate\Contracts\Auth\Factory::class,
            ],
            'cache' => [\Radiate\Cache\Repository::class],
            'config' => [
                \Radiate\Config\Repository::class,
                \Illuminate\Config\Repository::class,
                \Illuminate\Contracts\Config\Repository::class,
            ],
            'encrypter' => [
                \Radiate\Encryption\Encrypter::class,
                \Illuminate\Contracts\Encryption\Encrypter::class,
                \Illuminate\Contracts\Encryption\StringEncrypter::class,
            ],
            'events' => [\Radiate\Events\Dispatcher::class],
            'files' => [\Radiate\Filesystem\Filesystem::class],
            'hash' => [
                \Radiate\Hashing\Hasher::class,
                \Illuminate\Contracts\Hashing\Hasher::class,
            ],
            'mailer' => [\Radiate\Mail\Mailer::class],
            'request' => [\Radiate\Http\Request::class],
            'router' => [\Radiate\Routing\Router::class],
            'validator' => [\Radiate\Validation\Factory::class],
            'view' => [\Radiate\View\Factory::class],
        ] as $alias => $abstracts) {
            foreach ($abstracts as $abstract) {
                $this->alias($alias, $abstract);
            }
        }
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @return void
     */
    protected function loadConfigurationFiles()
    {
        $this->instance('config', $repository = new Repository());

        foreach ($this->getConfigurationFiles() as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Set the facade root
     *
     * @return void
     */
    protected function setFacadeRoot()
    {
        Facade::setFacadeApplication($this);
    }

    /**
     * Register the class aliases
     *
     * @param array $aliases
     * @return void
     */
    public function aliases(array $aliases = [])
    {
        foreach ($aliases as $alias => $class) {
            if ($this->registeredClassAliases[$alias]) {
                return;
            }
            if (class_alias($class, $alias)) {
                $this->registeredClassAliases[$alias] = $class;
            };
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles(): array
    {
        $files = [];

        $configPath = realpath($this->basePath('config'));

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param \SplFileInfo $file
     * @param string $configPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, string $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }

    /**
     * Get the app base path
     *
     * @return string
     */
    public function basePath(string $path = null)
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Register a service provider
     *
     * @param string $provider
     * @return void
     */
    public function register(string $provider)
    {
        $provider = new $provider($this);

        $provider->register();

        $this->providers[] = $provider;
    }

    /**
     * Boot the service providers
     *
     * @return void
     */
    protected function bootProviders()
    {
        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }

    /**
     * Capture the server request
     *
     * @return \Radiate\Http\Request
     */
    protected function captureRequest()
    {
        $this->instance('request', $request = Request::capture());

        return $request;
    }

    /**
     * Capture the server request
     *
     * @return void
     */
    protected function runRequestThroughStack(Request $request)
    {
        try {
            $response = (new Pipeline())
                ->send($request)
                ->through($this->middleware)
                ->then(function ($request) {
                    $this->instance('request', $request);

                    return $request;
                });
        } catch (Throwable $e) {
            $response = $this->renderException($request, $e);
        }

        return $response;
    }

    /**
     * Add a global middleware to the app
     *
     * @param array $middleware
     * @return self
     */
    public function middleware(array $middleware)
    {
        $this->middleware = array_unique(array_merge($this->middleware, $middleware));

        return $this;
    }

    /**
     * Add a global middleware to the app
     *
     * @param array $middleware
     * @return self
     */
    public function routeMiddleware(array $middleware)
    {
        $this->routeMiddleware = array_merge($this->routeMiddleware, $middleware);

        return $this;
    }

    /**
     * Get the route middleware
     *
     * @return array
     */
    public function getRouteMiddleware()
    {
        return $this->routeMiddleware;
    }

    /**
     * Boot the application
     *
     * @return void
     */
    public function boot()
    {
        $request = $this->captureRequest();

        $this->runRequestThroughStack($request);

        $this->bootProviders();

        if ($this->resolved('router')) {
            $this['router']->dispatch($request);
        }
    }

    /**
     * Get the app namespace
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace()
    {
        if ($this->namespace) {
            return $this->namespace;
        }

        $composer = json_decode($this['files']->get($this->basePath('composer.json')), true);

        if ($composer['autoload'] && $loader = $composer['autoload']['psr-4']) {
            foreach ($loader as $namespace => $path) {
                if (realpath($this->basePath('app')) === realpath($this->basePath($path))) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }

    /**
     * Determine if the app is running in the console
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return class_exists('WP_CLI');
    }

    /**
     * Render an HTTP exception
     *
     * @param \Radiate\Http\Request $request
     * @param \Throwable $e
     * @return string
     */
    public function renderException(Request $request, Throwable $e)
    {
        return $this[ExceptionHandler::class]->render($request, $e);
    }

    /**
     * Get or check the current application environment.
     *
     * @param  string|array|null  $environments
     * @return string|bool
     */
    public function environment($environments = null)
    {
        if ($environments) {
            return in_array($this['env'], (array) $environments);
        }

        return $this['env'];
    }

    /**
     * Determine if the app is in a local environment
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this['env'] == 'local';
    }

    /**
     * Determine if the app is in a development environment
     *
     * @return bool
     */
    public function isDevelopment()
    {
        return $this['env'] == 'development';
    }

    /**
     * Determine if the app is in a staging environment
     *
     * @return bool
     */
    public function isStaging()
    {
        return $this['env'] == 'staging';
    }

    /**
     * Determine if the app is in a production environment
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this['env'] == 'production';
    }
}
