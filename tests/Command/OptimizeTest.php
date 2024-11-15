<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();

    $this->artisan('optimize:clear')->execute();

    DomainCache::clear();
});

afterEach(function () {
    $this->artisan('optimize:clear')->execute();

    DomainCache::clear();
});

it('can optimize discovered domain providers, commands, migrations', function () {
    expect(DomainCache::get('domain-providers'))->toBeNull();
    expect(DomainCache::get('domain-commands'))->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->toBeNull();

    $this
        ->artisan('ddd:optimize')
        ->expectsOutputToContain('Caching DDD providers, commands, migration paths.')
        ->expectsOutputToContain('domain providers')
        ->expectsOutputToContain('domain commands')
        ->expectsOutputToContain('domain migration paths')
        ->execute();

    expect(DomainCache::get('domain-providers'))
        ->toContain('Domain\Invoicing\Providers\InvoiceServiceProvider');

    expect(DomainCache::get('domain-commands'))
        ->toContain('Domain\Invoicing\Commands\InvoiceDeliver');

    $paths = collect(DomainCache::get('domain-migration-paths'))->join("\n");

    expect($paths)->toContainFilepath('src/Domain/Invoicing/Database/Migrations');
});

it('can clear the cache', function () {
    Artisan::call('ddd:optimize');

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
    expect(DomainCache::get('domain-providers'))->toBeNull();
    expect(DomainCache::get('domain-commands'))->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->toBeNull();

    $this->artisan('ddd:optimize')->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

    $this->artisan('cache:clear')->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

    if (Feature::LaravelPackageOptimizeCommands->missing()) {
        $this->artisan('optimize:clear')->execute();

        expect(DomainCache::get('domain-providers'))->not->toBeNull();
        expect(DomainCache::get('domain-commands'))->not->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();
    }
});

describe('laravel optimize', function () {
    test('optimize will include ddd:optimize', function () {
        expect(DomainCache::get('domain-providers'))->toBeNull();
        expect(DomainCache::get('domain-commands'))->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->toBeNull();

        $this->artisan('optimize')->execute();

        expect(DomainCache::get('domain-providers'))->not->toBeNull();
        expect(DomainCache::get('domain-commands'))->not->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();
    });

    test('optimize:clear will clear ddd cache', function () {
        $this->artisan('ddd:optimize')->execute();

        expect(DomainCache::get('domain-providers'))->not->toBeNull();
        expect(DomainCache::get('domain-commands'))->not->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

        $this->artisan('optimize:clear')->execute();

        expect(DomainCache::get('domain-providers'))->toBeNull();
        expect(DomainCache::get('domain-commands'))->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->toBeNull();
    });
})->skipOnLaravelVersionsBelow(Feature::LaravelPackageOptimizeCommands->value);
