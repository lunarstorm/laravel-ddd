<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    $this->setupTestApplication();

    Config::set('ddd.domain_namespace', 'Domain');

    (new DomainAutoloader())->autoload();
});

it('can autoload domain policy', function ($class, $expectedPolicy) {
    expect(class_exists($class))->toBeTrue();
    expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
})->with([
    ['Domain\Invoicing\Models\Invoice', 'Domain\Invoicing\Policies\InvoicePolicy'],
]);

it('can autoload non-domain policy', function ($class, $expectedPolicy) {
    expect(class_exists($class))->toBeTrue();
    expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
})->with([
    ['App\Models\Post', 'App\Policies\PostPolicy'],
]);
