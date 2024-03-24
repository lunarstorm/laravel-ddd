<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class DomainListCommand extends Command
{
    protected $name = 'ddd:list';

    protected $description = 'List all current domains';
}
