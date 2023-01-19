<?php

use Illuminate\Support\Facades\Artisan;

it('can generate domain base model', function () {
    $modelName = 'BaseModel';
    $domain = 'Shared';

    $expectedModelPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.models'),
        "{$modelName}.php",
    ]));

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    expect(file_exists($expectedModelPath))->toBeFalse();

    Artisan::call("ddd:make:base-model {$domain} {$modelName}");

    expect(file_exists($expectedModelPath))->toBeTrue();

    $expectedClass = implode('\\', [
        'Domains',
        $domain,
        config('ddd.namespaces.models'),
        $modelName,
    ]);

    Artisan::call('ddd:install');

    new $expectedClass();
});
