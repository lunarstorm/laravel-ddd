<?php

use Illuminate\Support\Facades\Gate;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();
})->skip();

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
