<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    $this->setupTestApplication();

    Config::set('ddd.domain_namespace', 'Domain');
});

describe('autoload enabled', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.factories', true);

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('can resolve domain factory', function ($modelClass, $expectedFactoryClass) {
        expect($modelClass::factory())->toBeInstanceOf($expectedFactoryClass);
    })->with([
        // VanillaModel is a vanilla eloquent model in the domain layer
        ['Domain\Invoicing\Models\VanillaModel', 'Domain\Invoicing\Database\Factories\VanillaModelFactory'],

        // Invoice has a factory both in the domain layer and the old way, but domain layer should take precedence
        ['Domain\Invoicing\Models\Invoice', 'Domain\Invoicing\Database\Factories\InvoiceFactory'],

        // Payment has a factory not in the domain layer (the old way)
        ['Domain\Invoicing\Models\Payment', 'Database\Factories\Invoicing\PaymentFactory'],

        // A subdomain Internal\Reporting scenario
        ['Domain\Internal\Reporting\Models\Report', 'Domain\Internal\Reporting\Database\Factories\ReportFactory'],
    ]);

    it('gracefully falls back for non-domain factories', function () {
        Artisan::call('make:model RegularModel -f');

        $modelClass = 'App\Models\RegularModel';

        expect(class_exists($modelClass))->toBeTrue();

        expect($modelClass::factory())
            ->toBeInstanceOf('Database\Factories\RegularModelFactory');
    });
});

describe('autoload disabled', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.factories', false);

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('cannot resolve factories that rely on autoloading', function ($modelClass) {
        expect(fn () => $modelClass::factory())->toThrow(Error::class);
    })->with([
        ['Domain\Invoicing\Models\VanillaModel'],
        ['Domain\Internal\Reporting\Models\Report'],
    ]);
});
