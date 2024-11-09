<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainMigration;

class OptimizeCommand extends Command
{
    protected $name = 'ddd:optimize';

    protected $description = 'Cache auto-discovered domain objects and migration paths.';

    protected function configure()
    {
        $this->setAliases([
            'ddd:cache',
        ]);

        parent::configure();
    }

    public function handle()
    {
        $this->components->info('Caching DDD providers, commands, migration paths.');

        $this->components->task('domain providers', fn () => DomainAutoloader::cacheProviders());
        $this->components->task('domain commands', fn () => DomainAutoloader::cacheCommands());
        $this->components->task('domain migration paths', fn () => DomainMigration::cachePaths());

        $this->newLine();
    }
}