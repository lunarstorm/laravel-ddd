<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainSeederMakeCommand extends SeederMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:seeder';
}
