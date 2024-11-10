<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\JobMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainJobMakeCommand extends JobMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:job';
}
