<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainMigration;

class CacheCommand extends Command
{
    protected $name = 'ddd:cache';

    protected $description = 'Cache auto-discovered domain objects and migration paths.';

    public function handle()
    {
        $this->components->info('Caching DDD providers, commands, migration paths.');

        $this->components->task('domain providers', fn () => DomainAutoloader::cacheProviders());
        $this->components->task('domain commands', fn () => DomainAutoloader::cacheCommands());
        $this->components->task('domain migration paths', fn () => DomainMigration::cachePaths());

        $this->newLine();
    }
}
