<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    $this->setupTestApplication();

    Config::set('ddd.domain_namespace', 'Domain');

    (new DomainAutoloader())->autoload();
});

it('can autoload domain factory', function ($modelClass, $expectedFactoryClass) {
    expect($modelClass::factory())->toBeInstanceOf($expectedFactoryClass);
})->with([
    ['Domain\Invoicing\Models\VanillaModel', 'Domain\Invoicing\Database\Factories\VanillaModelFactory'],
    ['Domain\Internal\Reporting\Models\Report', 'Domain\Internal\Reporting\Database\Factories\ReportFactory'],
]);

it('does not affect non-domain model factories', function () {
    Artisan::call('make:model RegularModel -f');

    $modelClass = 'App\Models\RegularModel';

    expect(class_exists($modelClass))->toBeTrue();

    expect($modelClass::factory())
        ->toBeInstanceOf('Database\Factories\RegularModelFactory');
});
