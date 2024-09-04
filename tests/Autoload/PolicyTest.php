<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    $this->setupTestApplication();

    Config::set('ddd.domain_namespace', 'Domain');
    Config::set('ddd.autoload.factories', true);

    $this->afterApplicationCreated(function () {
        (new DomainAutoloader)->autoload();
    });
});

it('can autoload domain policy', function ($class, $expectedPolicy) {
    expect(class_exists($class))->toBeTrue();
    expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
})->with([
    ['Domain\Invoicing\Models\Invoice', 'Domain\Invoicing\Policies\InvoicePolicy'],
]);

it('gracefully falls back for non-domain policies', function ($class, $expectedPolicy) {
    expect(class_exists($class))->toBeTrue();
    expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
})->with([
    ['App\Models\Post', 'App\Policies\PostPolicy'],
]);
