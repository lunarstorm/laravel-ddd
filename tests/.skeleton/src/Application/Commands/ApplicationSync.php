<?php

namespace Application\Commands;

use Illuminate\Console\Command;
use Infrastructure\Models\AppSession;

class ApplicationSync extends Command
{
    protected $signature = 'application:sync';

    protected $description = 'Sync application state.';

    public function handle()
    {
        $this->info('Application state synced!');

        if ($secret = AppSession::getSecret()) {
            $this->line($secret);

            return;
        }
    }
}
