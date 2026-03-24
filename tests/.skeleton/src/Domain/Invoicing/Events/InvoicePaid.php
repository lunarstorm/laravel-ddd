<?php

namespace Domain\Invoicing\Events;

class InvoicePaid
{
    public function __construct(public array $data = [])
    {
    }
}
