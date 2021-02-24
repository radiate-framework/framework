<?php

namespace Radiate\Console;

use Radiate\Foundation\Application;
use Radiate\Support\Collection;
use Radiate\Support\Str;
use WP_CLI;
use function WP_CLI\Utils\format_items as wpcli_format_items;

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
     * The command synopsis.
     *
     * @var array
     */
    protected $synopsis = [];

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
     * The container instance
     *
     * @var \Radiate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->synopsis = $this->parseSignature();
    }

    /**
     * Register a command
     *
     * @return void
     */
    public function register()
    {
        WP_CLI::add_command('radiate ' . $this->name, $this, $this->synopsis);
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

        $this->app->call([$this, 'handle']);

        die;
    }

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
     * Display a message
     *
     * @param string $message
     * @return void
     */
    protected function line(string $message, string $style = null)
    {
        $message  = $style ? $style . $message . "\033[0m" : $message;

        $this->write($message);
    }

    /**
     * Output new lines
     *
     * @param integer $lines The number of new lines to output
     * @return void
     */
    protected function newLine(int $lines = 1)
    {
        $this->write(str_repeat(PHP_EOL, $lines));
    }

    /**
     * Write a message to the terminal
     *
     * @param string $message
     * @return void
     */
    protected function write(string $message)
    {
        fwrite(STDOUT, $message);
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message)
    {
        $this->line($message, "\033[32m");
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function comment(string $message)
    {
        $this->line($message, "\033[33m");
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message)
    {
        $this->line($message, "\033[41m");
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function warn(string $message)
    {
        $this->line($message, "\033[33m");
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function alert(string $message)
    {
        $length = Str::length(strip_tags($message)) + 12;

        $this->comment(str_repeat('*', $length));
        $this->comment('*     ' . $message . '     *');
        $this->comment(str_repeat('*', $length));

        $this->newLine();
    }

    /**
     * Ask the user to confirm an action.
     *
     * @param string $question The message to post to the console.
     * @param bool   $skip     Should the confirm be skipped?
     * @return bool
     */
    protected function confirm(string $question, bool $skip = false): bool
    {
        if (!$skip) {
            $answer = $this->ask($question . ' (yes/no)' . " \033[39m[\033[33mno\033[0m]");

            return in_array(strtolower($answer), ['yes', 'y']);
        }

        return true;
    }

    /**
     * Prompt the user with a question and return their answer.
     *
     * @param string $question The question to ask the user.
     * @return string
     */
    protected function question(string $question)
    {
        $this->line($question, "\033[30m\033[106m");
    }

    /**
     * Prompt the user with a question and return their answer.
     *
     * @param string $question The question to ask the user.
     * @return string
     */
    protected function ask(string $question)
    {
        fwrite(STDOUT, "\033[32m" . $question . "\033[0m\n> ");

        return trim(fgets(STDIN));
    }

    /**
     * Output a table
     *
     * @param array $headers The table headers
     * @param array $data    The table data
     * @return void
     */
    protected function table(array $headers, array $data)
    {
        wpcli_format_items('table', $data, $headers);
    }

    /**
     * Create a new progress bar
     *
     * @param integer $count The progress bar count
     * @return \Radiate\Console\ProgressBar
     */
    public function createProgressBar(int $count): ProgressBar
    {
        return new ProgressBar($count);
    }

    /**
     * Call a command
     *
     * @param string $message
     * @param array $args
     * @return void
     */
    protected function call(string $command, array $args = [])
    {
        $command = Collection::make($args)
            ->reduceWithKeys(function ($carry, $value, $key) {
                if (Str::startsWith($key, '--') && $value) {
                    $value = $value === true ? $key : $key . '=' . $value;
                }
                return $carry . ' ' . $value;
            }, $command);

        $this->run($command);
    }

    /**
     * Run a command
     *
     * @param string $message
     * @return void
     */
    protected function run(string $command)
    {
        WP_CLI::runcommand($command);
    }

    /**
     * Get all arguments.
     *
     * @return array
     */
    protected function arguments()
    {
        return $this->arguments;
    }

    /**
     * Determine if the argument is present
     *
     * @param string $key The argument name.
     * @return bool
     */
    protected function hasArgument(string $key)
    {
        return isset($this->arguments[$key]);
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
     * Get all options.
     *
     * @return array
     */
    protected function options()
    {
        return $this->options;
    }

    /**
     * Determine if the option is present
     *
     * @param string $key The option name.
     * @return bool
     */
    protected function hasOption(string $key)
    {
        return isset($this->options[$key]);
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

    /**
     * Set the container instance
     *
     * @param \Radiate\Foundation\Application $app
     * @return mixed
     */
    public function setContainer(Application $app)
    {
        $this->app = $app;

        return $this;
    }
}
