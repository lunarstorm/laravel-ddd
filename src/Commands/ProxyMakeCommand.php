<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\MailMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class ProxyMakeCommand extends MailMakeCommand
{
    use ResolvesDomainFromInput;
}
