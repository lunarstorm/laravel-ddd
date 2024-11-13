<?php

use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainCache;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    $this->setupTestApplication();
});

describe('without autoload', function () {
    beforeEach(function () {
        config([
            'ddd.autoload.providers' => false,
        ]);

        // $this->afterApplicationCreated(function () {
        //     (new DomainAutoloader)->autoload();
        // });

        (new DomainAutoloader)->autoload();
    });

    it('does not register the provider', function () {
        expect(fn () => app('invoicing'))->toThrow(Exception::class);
    });
});

describe('with autoload', function () {
    beforeEach(function () {
        config([
            'ddd.autoload.providers' => true,
        ]);

        // $this->afterApplicationCreated(function () {
        //     (new DomainAutoloader)->autoload();
        // });

        (new DomainAutoloader)->autoload();
    });

    it('registers the provider', function () {
        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');
    });
});

describe('caching', function () {
    beforeEach(function () {
        config([
            'ddd.autoload.providers' => true,
        ]);

        $this->setupTestApplication();
    });

    it('remembers the last cached state', function () {
        DomainCache::set('domain-providers', []);

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });

        expect(fn () => app('invoicing'))->toThrow(Exception::class);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-providers', []);
        DomainCache::clear();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });

        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');
    });
});
