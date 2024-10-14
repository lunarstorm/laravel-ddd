<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Support\Path;

beforeEach(function () {
    $this->setupTestApplication();
    DomainCache::clear();
});

it('can cache discovered domain providers and commands', function () {
    expect(DomainCache::get('domain-providers'))->toBeNull();
    expect(DomainCache::get('domain-commands'))->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->toBeNull();

    $this
        ->artisan('ddd:cache')
        ->expectsOutputToContain('Caching DDD providers, commands, migration paths.')
        ->expectsOutputToContain('domain providers')
        ->expectsOutputToContain('domain commands')
        ->expectsOutputToContain('domain migration paths')
        ->execute();

    expect(DomainCache::get('domain-providers'))
        ->toContain('Domain\Invoicing\Providers\InvoiceServiceProvider');

    expect(DomainCache::get('domain-commands'))
        ->toContain('Domain\Invoicing\Commands\InvoiceDeliver');

    expect(DomainCache::get('domain-migration-paths'))
        ->toContain(base_path(Path::normalize('src/Domain/Invoicing/Database/Migrations')));
});

it('can clear the cache', function () {
    Artisan::call('ddd:cache');

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

    $this
        ->artisan('ddd:clear')
        ->expectsOutputToContain('Domain cache cleared successfully.')
        ->execute();

    expect(DomainCache::get('domain-providers'))->toBeNull();
    expect(DomainCache::get('domain-commands'))->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->toBeNull();
});

it('will not be cleared by laravel cache clearing', function () {
    config(['cache.default' => 'file']);

    expect(DomainCache::get('domain-providers'))->toBeNull();
    expect(DomainCache::get('domain-commands'))->toBeNull();

    $this->artisan('ddd:cache')->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();

    $this->artisan('cache:clear')->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();

    $this->artisan('optimize:clear')->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();
});
