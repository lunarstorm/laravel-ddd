<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Tey\LaravelDDD\Facades\Autoload;
use Tey\LaravelDDD\Support\DomainMigration;

class OptimizeCommand extends Command
{
    protected $name = 'ddd:optimize';

    protected $description = 'Cache auto-discovered domain objects and migration paths.';

    protected function configure(): void
    {
        $this->setAliases([
            'ddd:cache',
        ]);

        parent::configure();
    }

    public function handle()
    {
        $this->components->info('Caching DDD providers, commands, listeners, migration paths.');
        $this->components->task('domain providers', fn () => Autoload::cacheProviders());
        $this->components->task('domain commands', fn () => Autoload::cacheCommands());
        $this->components->task('domain listeners', fn () => Autoload::cacheListeners());
        $this->components->task('domain migration paths', fn () => DomainMigration::cachePaths());
        $this->newLine();

        return self::SUCCESS;
    }
}
