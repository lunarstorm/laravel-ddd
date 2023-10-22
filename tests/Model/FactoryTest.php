<?php

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;

it('can resolve the factory name of a domain model', function ($modelClass, $expectedFactoryClass) {
    expect(DomainFactory::resolveFactoryName($modelClass))->toBe($expectedFactoryClass);
})->with([
    ["Domain\Customer\Models\Invoice", "Database\Factories\Customer\InvoiceFactory"],
    ["Domain\Reports\Accounting\Models\InvoiceReport", "Database\Factories\Reports\Accounting\InvoiceReportFactory"],
    ["App\Models\Invoice", null],
]);

it('can instantiate a domain model factory', function ($domainParameter, $modelName, $modelClass) {
    Artisan::call("ddd:model -f {$domainParameter} {$modelName}");
    expect(class_exists($modelClass))->toBeTrue();
    expect($modelClass::factory())->toBeInstanceOf(Factory::class);
})->with([
    // Domain, Model, Model FQN
    ['Fruits', 'Apple', 'Domain\Fruits\Models\Apple'],
    ['Fruits.Citrus', 'Lime', 'Domain\Fruits\Citrus\Models\Lime'],
]);
