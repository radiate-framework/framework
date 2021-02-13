<?php

namespace Radiate\Console;

use Radiate\Foundation\Application;
use WP_CLI;

abstract class Command
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The allowed arguments.
     *
     * @var array
     */
    protected $allowedArguments = [];

    /**
     * The console arguments.
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The console options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The application
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * Create the command instance
     *
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a command
     *
     * @return void
     */
    public function register()
    {
        $synopsis = $this->parseSignature();

        WP_CLI::add_command('radiate ' . $this->name, $this, $synopsis);
    }

    /**
     * Parse the signature into the command name arguments, options and synopsis.
     *
     * @return array
     */
    protected function parseSignature(): array
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);

        $this->name = $name;

        $this->allowedArguments = $arguments;

        return [
            'shortdesc' => $this->description,
            'synopsis'  => array_merge($arguments, $options),
        ];
    }

    /**
     * Set up the command arguments and options.
     *
     * @param array $arguments The console arguments.
     * @param array $options The console options and flags.
     * @return void
     */
    public function __invoke(array $arguments, array $options)
    {
        $this->arguments = $this->parseArguments($arguments);

        $this->options = $options;

        $this->handle();
    }

    /**
     * Handle the command call.
     *
     * @return void
     */
    abstract protected function handle();

    /**
     * Parse the arguments into a keyed array.
     *
     * @param array $args The arguments to parse.
     * @return array
     */
    protected function parseArguments(array $args)
    {
        $arguments = [];

        foreach ($this->allowedArguments as $index => $argument) {
            $arguments[$argument['name']] = $args[$index];
        }
        return $arguments;
    }

    /**
     * Display a success message
     *
     * @param string $message
     * @return void
     */
    protected function success(string $message)
    {
        WP_CLI::success($message);
    }

    /**
     * Display a message
     *
     * @param string $message
     * @return void
     */
    protected function line(string $message)
    {
        WP_CLI::log($message);
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message)
    {
        WP_CLI::error($message);
    }

    /**
     * Get a named argument.
     *
     * @param string $key The argument name.
     * @return mixed
     */
    protected function argument(string $key)
    {
        return $this->arguments[$key] ?? null;
    }

    /**
     * Get an option.
     *
     * @param string $key The option name.
     * @return mixed
     */
    protected function option(string $key)
    {
        return $this->options[$key] ?? null;
    }
}
