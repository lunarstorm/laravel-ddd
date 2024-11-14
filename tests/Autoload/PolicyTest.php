<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    $this->setupTestApplication();

    Config::set([
        'ddd.domain_path' => 'src/Domain',
        'ddd.domain_namespace' => 'Domain',
        'ddd.application_namespace' => 'Application',
        'ddd.application_path' => 'src/Application',
        'ddd.application_objects' => [
            'controller',
            'request',
            'middleware',
        ],
        'ddd.layers' => [
            'Infrastructure' => 'src/Infrastructure',
        ],
        'ddd.autoload.factories' => true,
    ]);

    $this->afterApplicationCreated(function () {
        (new DomainAutoloader)->autoload();
    });
});

it('can autoload policy', function ($class, $expectedPolicy) {
    expect(class_exists($class))->toBeTrue();
    expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
})->with([
    ['Domain\Invoicing\Models\Invoice', 'Domain\Invoicing\Policies\InvoicePolicy'],
    ['Infrastructure\Models\AppSession', 'Infrastructure\Policies\AppSessionPolicy'],
    ['Application\Models\Login', 'Application\Policies\LoginPolicy'],
]);

it('gracefully falls back for non-ddd policies', function ($class, $expectedPolicy) {
    expect(class_exists($class))->toBeTrue();
    expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
})->with([
    ['App\Models\Post', 'App\Policies\PostPolicy'],
]);
