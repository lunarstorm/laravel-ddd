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

    Artisan::call("ddd:base-model {$domain} {$modelName}");

    expect(file_exists($expectedModelPath))->toBeTrue();
});

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:base-model')
        ->expectsQuestion('What is the domain?', 'Shared')
        ->assertExitCode(0);
});
