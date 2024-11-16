<?php

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();
    Artisan::call('optimize:clear');
});

afterEach(function () {
    DomainCache::clear();
});

describe('when ddd.autoload.factories = true', function () {
    it('handles the factories', function () {
        config()->set('ddd.autoload.factories', true);

        $mock = AutoloadManager::partialMock();
        $mock->shouldReceive('handleFactories')->once();
        $mock->boot();
    });

    it('can resolve domain factory', function ($modelClass, $expectedFactoryClass) {
        config()->set('ddd.autoload.factories', true);

        $mock = AutoloadManager::partialMock();
        $mock->boot();

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
        config()->set('ddd.autoload.factories', true);

        $this->refreshApplication();

        Artisan::call('make:model RegularModel -f');

        $modelClass = 'App\Models\RegularModel';

        expect(class_exists($modelClass))->toBeTrue();

        expect(Factory::resolveFactoryName($modelClass))
            ->toEqual('Database\Factories\RegularModelFactory');
    });
});

describe('when ddd.autoload.factories = false', function () {
    it('skips handling factories', function () {
        config()->set('ddd.autoload.factories', false);

        $mock = AutoloadManager::partialMock();
        $mock->shouldNotReceive('handleFactories');
        $mock->boot();
    });

    it('cannot resolve factories that rely on autoloading', function ($modelClass, $correctFactories) {
        config()->set('ddd.autoload.factories', false);

        $mock = AutoloadManager::partialMock();
        $mock->boot();

        expect(fn () => $modelClass::factory())->toThrow(Error::class);
    })->with([
        ['Domain\Invoicing\Models\VanillaModel', ['Domain\Invoicing\Database\Factories\VanillaModelFactory', 'Database\Factories\Invoicing\VanillaModelFactory']],
        ['Domain\Internal\Reporting\Models\Report', ['Domain\Internal\Reporting\Database\Factories\ReportFactory', 'Database\Factories\Internal\Reporting\ReportFactory']],
    ]);
});
