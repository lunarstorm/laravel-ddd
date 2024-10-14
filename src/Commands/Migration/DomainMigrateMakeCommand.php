<?php

namespace Lunarstorm\LaravelDDD\Commands\Migration;

use Lunarstorm\LaravelDDD\Support\Path;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Commands\Migration\BaseMigrateMakeCommand;

class DomainMigrateMakeCommand extends BaseMigrateMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:migration';

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if ($this->domain) {
            return $this->laravel->basePath($this->domain->migrationPath);
        }

        return $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'migrations';
    }
}
