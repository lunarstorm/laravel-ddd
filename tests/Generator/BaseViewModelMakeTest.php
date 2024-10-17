<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

it('can generate base view model', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $className = 'ViewModel';
    $domain = 'Shared';

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.view_model'),
        "{$className}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:base-view-model {$domain}:{$className}");

    expect(Artisan::output())->toContainFilepath($relativePath);

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.view_model'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');
