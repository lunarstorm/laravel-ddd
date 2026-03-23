<?php

namespace Domain\Invoicing\Listeners;

use Domain\Invoicing\Events\InvoiceCreated;
use Domain\Invoicing\Events\InvoicePaid;
use Illuminate\Events\Dispatcher;

class InvoiceEventSubscriber
{
    public function handleInvoiceCreated(InvoiceCreated $event): void
    {
        // Handle creation
    }

    public function handleInvoicePaid(InvoicePaid $event): void
    {
        // Handle payment
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(InvoiceCreated::class, [self::class, 'handleInvoiceCreated']);
        $events->listen(InvoicePaid::class, [self::class, 'handleInvoicePaid']);
    }
}
