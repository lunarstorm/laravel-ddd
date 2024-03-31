<?php

namespace Domain\Invoicing\Commands;

use Domain\Invoicing\Models\Invoice;
use Illuminate\Console\Command;

class InvoiceDeliver extends Command
{
    protected $signature = 'invoice:deliver';

    protected $description = 'Deliver invoice.';

    public function handle()
    {
        $this->info('Invoice delivered!');

        if ($secret = Invoice::getSecret()) {
            $this->line($secret);

            return;
        }
    }
}
