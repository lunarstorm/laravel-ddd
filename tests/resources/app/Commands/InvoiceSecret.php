<?php

namespace App\Commands;

use Domain\Invoicing\Models\Invoice;
use Illuminate\Console\Command;

class InvoiceSecret extends Command
{
    protected $signature = 'invoice:secret';

    protected $description = 'Show the invoice secret.';

    public function handle()
    {
        $this->line(Invoice::getSecret() ?? 'Invoice secret not set.');
    }
}
