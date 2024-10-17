<?php

use Lunarstorm\LaravelDDD\Support\DomainResolver;

beforeEach(function () {
    $this->artisan('ddd:model', [
        'name' => 'Invoice',
        '--domain' => 'Invoicing',
    ]);

    $this->artisan('ddd:dto', [
        'name' => 'CustomerProfile',
        '--domain' => 'Customer',
    ]);

    $this->artisan('ddd:value', [
        'name' => 'Subtotal',
        '--domain' => 'Shared',
    ]);

    $this->expectedDomains = [
        'Customer',
        'Invoicing',
        'Shared',
    ];
});

it('can get the current domains', function () {
    expect(DomainResolver::domainChoices())->toEqualCanonicalizing($this->expectedDomains);
});
