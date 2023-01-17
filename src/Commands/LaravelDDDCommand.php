<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class LaravelDDDCommand extends Command
{
    public $signature = 'laravel-ddd';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
