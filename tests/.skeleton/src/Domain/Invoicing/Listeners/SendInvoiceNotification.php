<?php

namespace Domain\Invoicing\Listeners;

use Domain\Invoicing\Events\InvoiceCreated;

class SendInvoiceNotification
{
    public function handle(InvoiceCreated $event): void
    {
        // Listener logic
    }
}
