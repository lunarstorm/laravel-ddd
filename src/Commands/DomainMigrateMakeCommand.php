<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMigrateMakeCommand extends MigrateMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:migration';
}
