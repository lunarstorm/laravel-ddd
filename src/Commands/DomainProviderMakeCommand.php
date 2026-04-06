<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainProviderMakeCommand extends ProviderMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:provider';
}
