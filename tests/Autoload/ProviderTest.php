<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->providers = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Application\Providers\ApplicationServiceProvider',
        'Infrastructure\Providers\InfrastructureServiceProvider',
    ];

    $this->setupTestApplication();

    DomainCache::clear();
    Artisan::call('ddd:clear');
});

afterEach(function () {
    DomainCache::clear();
    Artisan::call('ddd:clear');
});

describe('when ddd.autoload.providers = false', function () {
    it('skips handling providers', function () {
        config()->set('ddd.autoload.providers', false);

        $mock = AutoloadManager::partialMock();
        $mock->shouldNotReceive('handleProviders');
        $mock->run();
    });

    it('does not register the providers', function () {
        config()->set('ddd.autoload.providers', false);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getRegisteredProviders())->toBeEmpty();
    });
});

describe('when ddd.autoload.providers = true', function () {
    it('handles the providers', function () {
        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->shouldReceive('handleProviders')->once();
        $mock->run();
    });

    it('registers the providers', function () {
        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        $expected = array_values($this->providers);
        $registered = array_values($mock->getRegisteredProviders());
        expect($registered)->toHaveCount(count($expected));
        expect($registered)->each(fn ($item) => $item->toBeIn($expected));
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-providers', []);

        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        $registered = array_values($mock->getRegisteredProviders());
        expect($registered)->toHaveCount(0);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-providers', []);
        DomainCache::clear();

        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        $expected = array_values($this->providers);
        $registered = array_values($mock->getRegisteredProviders());
        expect($registered)->toHaveCount(count($expected));
        expect($registered)->each(fn ($item) => $item->toBeIn($expected));
    });
});
