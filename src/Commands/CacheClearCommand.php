<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainCache;

class CacheClearCommand extends Command
{
    protected $name = 'ddd:clear';

    protected $description = 'Clear cached domain autoloaded objects.';

    public function handle()
    {
        DomainCache::clear();

        $this->components->info('Domain cache cleared successfully.');
    }
}
