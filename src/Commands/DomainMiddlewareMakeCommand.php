<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMiddlewareMakeCommand extends MiddlewareMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:middleware';
}
