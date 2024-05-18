<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMigrationMakeCommand extends MigrateMakeCommand
{
    /*
    use ResolvesDomainFromInput {
        ResolvesDomainFromInput::getPath as getDomainPath;
    }
    */
    use ResolvesDomainFromInput;

    protected $signature = 'ddd:migration {name : The name of the migration}
        {--domain= : test}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration (Deprecated)}';

    protected $description = 'Generate a domain migration';

    protected function getMigrationPath()
    {
        //dd(dirname($this->getPath('test')));
        return dirname($this->getPath('test'));

        dump('mmmm', $this->getDomainPath(''), dirname($this->getDomainPath('')));

        dump('mmmm', $this->getDomainPath(''), dirname($this->getDomainPath('')));

        return dirname($this->getDomainPath(''));
    }

    protected function guessObjectType(): string
    {
        return 'migration';
    }
}
