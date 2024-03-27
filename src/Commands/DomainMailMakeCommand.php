<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\MailMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMailMakeCommand extends MailMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:mail';
}
