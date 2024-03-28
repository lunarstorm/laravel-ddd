<?php

use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    $this->setupTestApplication();

    DomainAutoloader::clearCache();
});

describe('without autoload', function () {
    it('does not register the provider', function () {
        expect(fn () => app('invoicing'))->toThrow(Exception::class);
    });
});

describe('with autoload', function () {
    beforeEach(function () {
        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('registers the provider', function () {
        expect(app('invoicing'))->toEqual('invoicing-singleton');
        $this->artisan('invoice:deliver')->expectsOutputToContain('invoice-secret');
    });
});
