<?php

use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();

    DomainCache::clear();

    $this->originalComposerContents = file_get_contents(base_path('composer.json'));
});

afterEach(function () {
    DomainCache::clear();

    file_put_contents(base_path('composer.json'), $this->originalComposerContents);
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
    $this->artisan('ddd:optimize')->assertSuccessful()->execute();

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

    $this->artisan('ddd:optimize')->assertSuccessful()->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

    $this->artisan('cache:clear')->assertSuccessful()->execute();

    expect(DomainCache::get('domain-providers'))->not->toBeNull();
    expect(DomainCache::get('domain-commands'))->not->toBeNull();
    expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

    if (Feature::LaravelPackageOptimizeCommands->missing()) {
        $this->artisan('optimize:clear')->assertSuccessful()->execute();

        expect(DomainCache::get('domain-providers'))->not->toBeNull();
        expect(DomainCache::get('domain-commands'))->not->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();
    }
});

describe('laravel optimize', function () {
    beforeEach(function () {
        $this->artisan('optimize:clear')->assertSuccessful()->execute();
        config()->set('data.structure_caching.enabled', false);
    });

    afterEach(function () {
        $this->artisan('optimize:clear')->assertSuccessful()->execute();
    });

    test('optimize will include ddd:optimize', function () {
        expect(DomainCache::get('domain-providers'))->toBeNull();
        expect(DomainCache::get('domain-commands'))->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->toBeNull();

        $this->artisan('optimize')->assertSuccessful()->execute();

        expect(DomainCache::get('domain-providers'))->not->toBeNull();
        expect(DomainCache::get('domain-commands'))->not->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

        $this->artisan('optimize:clear')->assertSuccessful()->execute();
    });

    test('optimize:clear will clear ddd cache', function () {
        $this->artisan('ddd:optimize')->assertSuccessful()->execute();

        expect(DomainCache::get('domain-providers'))->not->toBeNull();
        expect(DomainCache::get('domain-commands'))->not->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->not->toBeNull();

        $this->artisan('optimize:clear')->assertSuccessful()->execute();

        expect(DomainCache::get('domain-providers'))->toBeNull();
        expect(DomainCache::get('domain-commands'))->toBeNull();
        expect(DomainCache::get('domain-migration-paths'))->toBeNull();

        $this->artisan('optimize:clear')->assertSuccessful()->execute();
    });
})->skipOnLaravelVersionsBelow(Feature::LaravelPackageOptimizeCommands->value);
