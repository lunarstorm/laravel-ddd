<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\DomainCache;

beforeEach(function () {
    $this->setupTestApplication();
    DomainCache::clear();
});

it('can cache discovered domain providers and commands', function () {
    expect(DomainCache::get('domain-providers'))->toBeNull();

    expect(DomainCache::get('domain-commands'))->toBeNull();

    $this
        ->artisan('ddd:cache')
        ->expectsOutput('Cached domain providers.')
        ->expectsOutput('Cached domain commands.')
        ->execute();

    expect(DomainCache::get('domain-providers'))
        ->toContain('Domain\Invoicing\Providers\InvoiceServiceProvider');

    expect(DomainCache::get('domain-commands'))
        ->toContain('Domain\Invoicing\Commands\InvoiceDeliver');
});

it('can clear the cache', function () {
    Artisan::call('ddd:cache');

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();

    $this
        ->artisan('ddd:clear')
        ->expectsOutput('Domain cache cleared.')
        ->execute();

    expect(DomainCache::get('domain-providers'))->toBeNull();
    expect(DomainCache::get('domain-commands'))->toBeNull();
});
