<?php

use Illuminate\Support\Facades\Artisan;

it('can generate base view model', function () {
    $className = 'ViewModel';
    $domain = 'Shared';

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.view_models'),
        "{$className}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:base-view-model {$domain} {$className}");

    expect(file_exists($expectedPath))->toBeTrue();
});