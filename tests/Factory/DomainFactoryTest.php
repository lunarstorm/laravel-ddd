<?php

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;

it('can resolve the factory name of a domain model', function ($modelClass, $expectedFactoryClass) {
    $this->setupTestApplication();

    expect(DomainFactory::resolveFactoryName($modelClass))->toBe($expectedFactoryClass);
})->with([
    ["Domain\Invoicing\Models\Invoice", "Domain\Invoicing\Database\Factories\InvoiceFactory"],
    ["Domain\Invoicing\Models\VanillaModel", "Domain\Invoicing\Database\Factories\VanillaModelFactory"],
    ["App\Models\Invoice", null],
]);

it('is backwards compatible with factories located in database/factories/**/*', function ($modelClass, $expectedFactoryClass) {
    $this->setupTestApplication();

    expect(DomainFactory::resolveFactoryName($modelClass))->toBe($expectedFactoryClass);
})->with([
    ["Domain\Customer\Models\Customer", "Database\Factories\Customer\CustomerFactory"],
    ["Domain\Reports\Accounting\Models\InvoiceReport", "Database\Factories\Reports\Accounting\InvoiceReportFactory"],
    ["Domain\Invoicing\Models\Payment", "Database\Factories\Invoicing\PaymentFactory"],
]);

it('can instantiate a domain model factory', function ($domainParameter, $modelName, $modelClass) {
    $this->setupTestApplication();

    Config::set('ddd.base_model', 'Lunarstorm\LaravelDDD\Models\DomainModel');
    Artisan::call("ddd:model -f {$domainParameter}:{$modelName}");

    expect(class_exists($modelClass))->toBeTrue();
    expect($modelClass::factory())->toBeInstanceOf(Factory::class);
})->with([
    // Domain, Model, Model FQN
    ['Fruits', 'Apple', 'Domain\Fruits\Models\Apple'],
    ['Fruits.Citrus', 'Lime', 'Domain\Fruits\Citrus\Models\Lime'],
]);
