<?php

use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();
    DomainCache::clear();
});

afterEach(function () {
    DomainCache::clear();
});

describe('without autoload', function () {
    it('does not register the provider', function ($binding) {
        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => false,
        ]);

        expect(fn () => app($binding))->toThrow(Exception::class);
    })->with([
        ['invoicing'],
        ['application-layer'],
        ['infrastructure-layer'],
    ]);
})->skip();

describe('with autoload', function () {
    it('registers the provider in domain layer', function () {
        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => true,
        ]);

        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');
    });

    it('registers the provider in application layer', function () {
        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => true,
        ]);

        expect(app('application-layer'))->toEqual('application-layer-singleton');
        $this->artisan('application:sync')->expectsOutputToContain('application-secret');
    });

    it('registers the provider in custom layer', function () {
        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => true,
        ]);

        expect(app('infrastructure-layer'))->toEqual('infrastructure-layer-singleton');
        $this->artisan('log:prune')->expectsOutputToContain('infrastructure-secret');
    });
})->skip();

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-providers', []);

        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => true,
        ]);

        expect(fn () => app('invoicing'))->toThrow(Exception::class);
        expect(fn () => app('application-layer'))->toThrow(Exception::class);
        expect(fn () => app('infrastructure-layer'))->toThrow(Exception::class);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-providers', []);
        DomainCache::clear();

        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => true,
        ]);

        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');

        expect(app('application-layer'))->toEqual('application-layer-singleton');
        $this->artisan('application:sync')->expectsOutputToContain('application-secret');

        expect(app('infrastructure-layer'))->toEqual('infrastructure-layer-singleton');
        $this->artisan('log:prune')->expectsOutputToContain('infrastructure-secret');
    });
})->skip();
