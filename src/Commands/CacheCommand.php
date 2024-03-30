<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

class CacheCommand extends Command
{
    protected $name = 'ddd:cache';

    protected $description = 'Cache auto-discovered domain objects used for autoloading.';

    public function handle()
    {
        DomainAutoloader::cacheProviders();

        $this->components->info('Domain providers cached successfully.');

        DomainAutoloader::cacheCommands();

        $this->components->info('Domain commands cached successfully.');
    }
}
