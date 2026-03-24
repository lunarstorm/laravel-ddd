<?php

namespace Domain\Invoicing\Events;

class InvoiceCreated
{
    public function __construct(public array $data = [])
    {
    }
}
