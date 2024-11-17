<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Support\Path;
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

    expect(config('ddd.autoload_ignore'))->toEqualCanonicalizing([
        'Tests',
        'Database/Migrations',
    ]);
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

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        collect($mock->getAllLayerPaths())
            ->each(fn ($path) => expect(is_dir($path))->toBeTrue("{$path} is not a directory"));

        expect($mock->getRegisteredProviders())->toBeEmpty();
    });
});

describe('when ddd.autoload.providers = true', function () {
    it('handles the providers', function () {
        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->shouldReceive('handleProviders')->once();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        collect($mock->getAllLayerPaths())
            ->each(fn ($path) => expect(is_dir($path))->toBeTrue("{$path} is not a directory"));
    });

    it('registers the providers', function () {
        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        collect($mock->getAllLayerPaths())
            ->each(fn ($path) => expect(is_dir($path))->toBeTrue("{$path} is not a directory"));

        $expected = array_values($this->providers);
        $registered = array_values($mock->getRegisteredProviders());
        expect($mock->discoverProviders())->toEqualCanonicalizing($expected);
        expect($expected)->each(fn ($item) => $item->toBeIn($registered));
        expect($registered)->toHaveCount(count($expected));
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-providers', []);

        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        collect($mock->getAllLayerPaths())
            ->each(fn ($path) => expect(is_dir($path))->toBeTrue("{$path} is not a directory"));

        $registered = array_values($mock->getRegisteredProviders());
        expect($registered)->toHaveCount(0);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-providers', []);
        DomainCache::clear();

        config()->set('ddd.autoload.providers', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        collect($mock->getAllLayerPaths())
            ->each(fn ($path) => expect(is_dir($path))->toBeTrue("{$path} is not a directory"));

        $expected = array_values($this->providers);
        $registered = array_values($mock->getRegisteredProviders());
        expect($mock->discoverProviders())->toEqualCanonicalizing($expected);
        expect($expected)->each(fn ($item) => $item->toBeIn($registered));
        expect($registered)->toHaveCount(count($expected));
    });
});
