<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\MailMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMailMakeCommand extends MailMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:mail';
}
