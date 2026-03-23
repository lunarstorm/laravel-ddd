<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainCache;

class OptimizeClearCommand extends Command
{
    protected $name = 'ddd:clear';

    protected $description = 'Clear cached domain autoloaded objects.';

    protected function configure(): void
    {
        $this->setAliases([
            'ddd:optimize:clear',
        ]);

        parent::configure();
    }

    public function handle()
    {
        DomainCache::clear();

        $this->components->info('Domain cache cleared successfully.');

        return self::SUCCESS;
    }
}
