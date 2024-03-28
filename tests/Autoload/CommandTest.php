<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Symfony\Component\Console\Exception\CommandNotFoundException;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    $this->setupTestApplication();

    DomainAutoloader::clearCache();
});

describe('without autoload', function () {
    it('does not register the command', function () {
        expect(class_exists('Domain\Invoicing\Commands\InvoiceDeliver'))->toBeTrue();
        expect(fn () => Artisan::call('invoice:deliver'))->toThrow(CommandNotFoundException::class);
    });
});

describe('with autoload', function () {
    beforeEach(function () {
        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('registers the command', function () {
        expect(class_exists('Domain\Invoicing\Commands\InvoiceDeliver'))->toBeTrue();
        Artisan::call('invoice:deliver');
        expect(Artisan::output())->toContain('Invoice delivered!');
    });
});
