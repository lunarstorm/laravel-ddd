<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\JobMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainJobMakeCommand extends JobMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:job';
}
