<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\TraitMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainTraitMakeCommand extends TraitMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:trait';
}
