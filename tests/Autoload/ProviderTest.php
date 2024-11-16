<?php

use Lunarstorm\LaravelDDD\Facades\Autoload;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Mockery\MockInterface;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->providers = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Application\Providers\ApplicationServiceProvider',
        'Infrastructure\Providers\InfrastructureServiceProvider',
    ];

    $this->setupTestApplication();
});

afterEach(function () {
    DomainCache::clear();
});

describe('when ddd.autoload.providers = false', function () {
    it('skips handling providers', function () {
        config()->set('ddd.autoload.providers', false);

        $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldNotReceive('handleProviders');
        });

        $mock->boot();

        // collect($this->providers)->each(
        //     fn ($provider) => expect(app()->getProvider($provider))->toBeNull()
        // );
    });

    it('does not register the providers', function () {
        config()->set('ddd.autoload.providers', false);

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect($mock->getRegisteredProviders())->toBeEmpty();

        Autoload::boot();

        collect($this->providers)->each(
            fn ($provider) => expect(app()->getProvider($provider))->toBeNull()
        );
    });
});

describe('when ddd.autoload.providers = true', function () {
    it('handles the providers', function () {
        config()->set('ddd.autoload.providers', true);

        $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('handleProviders')->once();
        });

        $mock->boot();
    });

    it('registers the providers', function () {
        config()->set('ddd.autoload.providers', true);

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect(array_values($mock->getRegisteredProviders()))->toEqualCanonicalizing($this->providers);

        Autoload::boot();

        collect($this->providers)->each(
            fn ($provider) => expect(app()->getProvider($provider))->toBeInstanceOf($provider)
        );
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-providers', []);

        config()->set('ddd.autoload.providers', true);

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect(array_values($mock->getRegisteredProviders()))->toEqualCanonicalizing([]);

        Autoload::boot();

        collect($this->providers)->each(
            fn ($provider) => expect(app()->getProvider($provider))->toBeNull()
        );
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-providers', []);
        DomainCache::clear();

        config()->set('ddd.autoload.providers', true);

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect(array_values($mock->getRegisteredProviders()))->toEqualCanonicalizing($this->providers);

        Autoload::boot();

        collect($this->providers)->each(
            fn ($provider) => expect(app()->getProvider($provider))->toBeInstanceOf($provider)
        );
    });
});
