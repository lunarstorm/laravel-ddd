<?php

use Illuminate\Support\Facades\Gate;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->policies = [
        'Domain\Invoicing\Models\Invoice' => 'Domain\Invoicing\Policies\InvoicePolicy',
        'Infrastructure\Models\AppSession' => 'Infrastructure\Policies\AppSessionPolicy',
        'Application\Models\Login' => 'Application\Policies\LoginPolicy',
    ];

    $this->setupTestApplication();
});

afterEach(function () {
    DomainCache::clear();
});

describe('when ddd.autoload.policies = false', function () {
    it('skips handling policies', function () {
        config()->set('ddd.autoload.policies', false);

        $mock = AutoloadManager::partialMock();
        $mock->shouldNotReceive('handlePolicies');
        $mock->boot();
    });
});

describe('when ddd.autoload.policies = true', function () {
    it('handles policies', function () {
        config()->set('ddd.autoload.policies', true);

        $mock = AutoloadManager::partialMock();
        $mock->shouldReceive('handlePolicies')->once();
        $mock->boot();
    });

    it('can resolve the policies', function () {
        config()->set('ddd.autoload.policies', true);

        $mock = AutoloadManager::partialMock();
        $mock->boot();

        foreach ($this->policies as $class => $expectedPolicy) {
            $resolvedPolicy = Gate::getPolicyFor($class);
            expect($mock->getResolvedPolicies())->toHaveKey($class);
        }
    })->markTestIncomplete('custom layer policies are not yet supported');

    it('gracefully falls back for non-ddd policies', function ($class, $expectedPolicy) {
        config()->set('ddd.autoload.policies', true);

        $mock = AutoloadManager::partialMock();
        $mock->boot();

        expect(class_exists($class))->toBeTrue();
        expect(Gate::getPolicyFor($class))->toBeInstanceOf($expectedPolicy);
        expect($mock->getResolvedPolicies())->not->toHaveKey($class);
    })->with([
        ['App\Models\Post', 'App\Policies\PostPolicy'],
    ]);
});
