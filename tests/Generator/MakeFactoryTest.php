<?php

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate domain factories', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());
    $factoryName = "{$modelName}Factory";
    $domainHelper = new Domain($domain);
    $namespacedModel = $domainHelper->namespacedModel($modelName);

    // Domain factories are expected to be generated in:
    // database/factories/{Domain}/{Factory}.php

    $relativePath = implode('/', [
        'database/factories',
        $domain,
        "{$factoryName}.php",
    ]);

    $expectedFactoryPath = base_path($relativePath);

    if (file_exists($expectedFactoryPath)) {
        unlink($expectedFactoryPath);
    }

    expect(file_exists($expectedFactoryPath))->toBeFalse();

    Artisan::call("ddd:factory {$domain} {$factoryName}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContain($relativePath),
    );

    expect(file_exists($expectedFactoryPath))->toBeTrue(
        "Expected factory to be generated in {$expectedFactoryPath}"
    );

    $expectedNamespace = implode('\\', [
        'Database',
        'Factories',
        $domain,
    ]);

    $contents = file_get_contents($expectedFactoryPath);

    expect($contents)
        ->toContain("namespace {$expectedNamespace};")
        ->toContain("use {$namespacedModel};")
        ->toContain("protected \$model = {$modelName}::class;");
})->with('domainPaths');
