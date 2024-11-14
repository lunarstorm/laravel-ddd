<?php

use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    // $this->refreshApplicationWithConfig([
    //     'ddd.domain_path' => 'src/Domain',
    //     'ddd.domain_namespace' => 'Domain',
    //     'ddd.application_namespace' => 'Application',
    //     'ddd.application_path' => 'src/Application',
    //     'ddd.application_objects' => [
    //         'controller',
    //         'request',
    //         'middleware',
    //     ],
    //     'ddd.layers' => [
    //         'Infrastructure' => 'src/Infrastructure',
    //     ],
    //     'ddd.autoload_ignore' => [
    //         'Tests',
    //         'Database/Migrations',
    //     ],
    //     'cache.default' => 'file',
    // ]);

    $this->setupTestApplication();
});

// afterEach(function () {
//     $this->setupTestApplication();
// });

describe('without autoload', function () {
    it('does not register the provider', function ($binding) {
        // setConfigValues([
        //     'ddd.autoload.providers' => false,
        // ]);

        $this->afterApplicationRefreshed(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => false,
        ]);

        expect(DDD::autoloader()->getDiscoveredProviders())->toBeEmpty();

        expect(fn () => app($binding))->toThrow(Exception::class);
    })->with([
        ['invoicing'],
        ['application-layer'],
        ['infrastructure-layer'],
    ]);
});

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
        $this->afterApplicationCreated(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.providers' => true,
        ]);

        expect(app('infrastructure-layer'))->toEqual('infrastructure-layer-singleton');
        $this->artisan('log:prune')->expectsOutputToContain('infrastructure-secret');
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-providers', []);

        $this->afterApplicationCreated(function () {
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

        $this->afterApplicationCreated(function () {
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
});
