<?php

namespace Radiate\Foundation\Console;

use Radiate\Console\Command;
use Radiate\Support\Collection;
use Radiate\Support\Str;

class ListCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line("Radiate Framework");
        $this->newLine();

        $longest = 0;

        Collection::make($this->getSubcommands())
            ->map(function ($subcommand) use (&$longest) {
                if (strlen($name = $subcommand->get_name()) > $longest) {
                    $longest = strlen($name);
                }

                return $this->parseSubcommand($subcommand);
            })
            ->groupBy('group')
            ->sortKeys()
            ->each(function ($subcommands, $key) use ($longest) {
                $this->comment($key);
                $subcommands->each(function ($subcommand) use ($longest) {
                    $padded = Str::padRight($subcommand['name'], $longest + 2);
                    $this->info(" <info>{$padded}</info>{$subcommand['description']}");
                });
            });
    }

    /**
     * Get the subcommands
     *
     * @return array
     */
    protected function getSubcommands()
    {
        [$command] = \WP_CLI::get_runner()->find_command_to_run(['radiate']);

        return $command->get_subcommands();
    }

    /**
     * Parse the subcommand into a group with name and description
     *
     * @param mixed $subcommand
     * @return array
     */
    protected function parseSubcommand($subcommand)
    {
        $name = $subcommand->get_name();

        $group = ($parts = explode(':', $name)) && ($parts[1]) ? $parts[0] : 'Available commands:';

        return [
            'group'       => $group,
            'name'        => $name,
            'description' => $subcommand->get_shortdesc(),
        ];
    }
}
