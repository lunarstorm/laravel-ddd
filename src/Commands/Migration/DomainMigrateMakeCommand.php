<?php

namespace Lunarstorm\LaravelDDD\Commands\Migration;

use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Support\Path;

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
        if ($this->blueprint) {
            return $this->laravel->basePath($this->blueprint->getMigrationPath());
        }

        return $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations';
    }
}
