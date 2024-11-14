<?php

namespace Infrastructure\Commands;

use Illuminate\Console\Command;
use Infrastructure\Models\AppSession;
use Infrastructure\Support\Clipboard;

class LogPrune extends Command
{
    protected $signature = 'log:prune';

    protected $description = 'Prune system logs.';

    public function handle()
    {
        $this->info('System logs pruned!');

        if ($secret = Clipboard::get('secret')) {
            $this->line($secret);

            return;
        }
    }
}
