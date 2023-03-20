<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can generate view models', function () {
    $viewModelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.view_models'),
        "{$viewModelName}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:view-model {$domain} {$viewModelName}");

    expect(file_exists($expectedPath))->toBeTrue();
});

it('can generate view models in custom domain folder', function () {
    $customDomainPath = 'Custom/Domains';

    Config::set('ddd.paths.domains', $customDomainPath);

    $viewModelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        $customDomainPath,
        $domain,
        config('ddd.namespaces.view_models'),
        "{$viewModelName}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:view-model {$domain} {$viewModelName}");

    expect(file_exists($expectedPath))->toBeTrue();
});

it('normalizes generated view model to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.view_models'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:view-model {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with([
    'summaryViewModel' => ['summaryViewModel', 'SummaryViewModel'],
    'ShowInvoiceViewModel' => ['ShowInvoiceViewModel', 'ShowInvoiceViewModel'],
    'show-invoice-view-model' => ['show-invoice-view-model', 'ShowInvoiceViewModel'],
]);
