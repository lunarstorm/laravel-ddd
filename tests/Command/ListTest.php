<?php

use Illuminate\Support\Arr;
use Lunarstorm\LaravelDDD\Support\Path;

beforeEach(function () {
    $this->setupTestApplication();

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

it('can list domains', function () {
    $expectedTableContent = collect($this->expectedDomains)
        ->map(function (string $name) {
            return [
                $name,
                "Domain\\{$name}",
                Path::normalize("src/Domain/{$name}"),
            ];
        })
        ->toArray();

    $this
        ->artisan('ddd:list')
        ->expectsOutputToContain(...[
            'Domain',
            'Namespace',
            'Path',
            ...Arr::flatten($expectedTableContent),
        ]);
});
