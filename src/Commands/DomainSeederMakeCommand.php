<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainSeederMakeCommand extends SeederMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:seeder';
}
