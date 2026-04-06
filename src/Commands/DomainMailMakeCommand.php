<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\MailMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMailMakeCommand extends MailMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:mail';
}
