<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can generate domain models', function () {
    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

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

    Artisan::call("ddd:model {$domain} {$modelName}");

    expect(file_exists($expectedModelPath))->toBeTrue();
});

it('can generate domain models in custom domain folder', function () {
    $customDomainPath = 'Custom/Domains';

    Config::set('ddd.paths.domains', $customDomainPath);

    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedModelPath = base_path(implode('/', [
        $customDomainPath,
        $domain,
        config('ddd.namespaces.models'),
        "{$modelName}.php",
    ]));

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    expect(file_exists($expectedModelPath))->toBeFalse();

    Artisan::call("ddd:model {$domain} {$modelName}");

    expect(file_exists($expectedModelPath))->toBeTrue();
});

it('normalizes generated model to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedModelPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.models'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:model {$domain} {$given}");

    expect(file_exists($expectedModelPath))->toBeTrue();
})->with([
    'apple' => ['apple', 'Apple'],
    'Apple' => ['Apple', 'Apple'],
    'appleBottom' => ['appleBottom', 'AppleBottom'],
    'AppleBottom' => ['AppleBottom', 'AppleBottom'],
    'apple-bottom' => ['apple-bottom', 'AppleBottom'],
]);

it('generates the base model if needed', function () {
    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

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

    // This currently only tests for the default base model
    $expectedBaseModelPath = base_path('src/Domains/Shared/Models/BaseModel.php');

    // Todo: should bypass base model creation if
    // a custom base model is being used.
    // $baseModel = config('ddd.base_model');

    expect(file_exists($expectedBaseModelPath))->toBeFalse();

    Artisan::call("ddd:model {$domain} {$modelName}");

    expect(file_exists($expectedBaseModelPath))->toBeTrue();
});
