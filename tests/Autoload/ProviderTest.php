<?php

use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainCache;

beforeEach(function () {
    Config::set([
        'ddd.domain_path' => 'src/Domain',
        'ddd.domain_namespace' => 'Domain',
        'ddd.application_namespace' => 'Application',
        'ddd.application_path' => 'src/Application',
        'ddd.application_objects' => [
            'controller',
            'request',
            'middleware',
        ],
        'ddd.layers' => [
            'Infrastructure' => 'src/Infrastructure',
        ],
        'ddd.autoload_ignore' => [
            'Tests',
            'Database/Migrations',
        ],
        'cache.default' => 'file',
    ]);

    $this->setupTestApplication();
});

afterEach(function () {
    $this->setupTestApplication();
});

describe('without autoload', function () {
    beforeEach(function () {
        config([
            'ddd.autoload.providers' => false,
        ]);

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

        (new DomainAutoloader)->autoload();
    });

    it('registers the provider in domain layer', function () {
        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');
    });

    it('registers the provider in application layer', function () {
        expect(app('application-layer'))->toEqual('application-layer-singleton');
        $this->artisan('application:sync')->expectsOutputToContain('application-secret');
    });

    it('registers the provider in custom layer', function () {
        expect(app('infrastructure-layer'))->toEqual('infrastructure-layer-singleton');
        $this->artisan('log:prune')->expectsOutputToContain('infrastructure-secret');
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
        expect(fn () => app('application-layer'))->toThrow(Exception::class);
        expect(fn () => app('infrastructure-layer'))->toThrow(Exception::class);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-providers', []);
        DomainCache::clear();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });

        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');

        expect(app('application-layer'))->toEqual('application-layer-singleton');
        $this->artisan('application:sync')->expectsOutputToContain('application-secret');

        expect(app('infrastructure-layer'))->toEqual('infrastructure-layer-singleton');
        $this->artisan('log:prune')->expectsOutputToContain('infrastructure-secret');
    });
});
