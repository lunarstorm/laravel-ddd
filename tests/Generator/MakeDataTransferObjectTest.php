<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can generate data transfer objects', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $dtoName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.data_transfer_objects'),
        "{$dtoName}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:dto {$domain} {$dtoName}");

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.data_transfer_objects'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('normalizes generated data transfer object to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.data_transfer_objects'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:dto {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with([
    'payload' => ['payload', 'Payload'],
    'Payload' => ['Payload', 'Payload'],
    'invoicePayload' => ['invoicePayload', 'InvoicePayload'],
    'InvoicePayload' => ['InvoicePayload', 'InvoicePayload'],
    'invoice-payload' => ['invoice-payload', 'InvoicePayload'],
]);
