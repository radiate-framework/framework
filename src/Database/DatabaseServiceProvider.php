<?php

namespace Radiate\Database;

use Radiate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot(): void
    {
        $this->commands([
            \Radiate\Database\Console\MakeModel::class,
        ]);
    }
}
