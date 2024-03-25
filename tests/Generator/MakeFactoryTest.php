<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\Path;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate domain factories', function ($domainPath, $domainRoot, $domain, $subdomain) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $modelName = Str::studly(fake()->word());

    $factoryName = "{$modelName}Factory";

    $domainArgument = str($domain)
        ->when($subdomain, fn ($domain) => $domain->append("\\{$subdomain}"))
        ->toString();

    $domain = new Domain($domainArgument);

    $domainModel = $domain->model($modelName);

    $domainFactory = $domain->factory($factoryName);

    $expectedFactoryPath = Path::normalize(base_path($domainFactory->path));

    if (file_exists($expectedFactoryPath)) {
        unlink($expectedFactoryPath);
    }

    expect(file_exists($expectedFactoryPath))->toBeFalse();

    Artisan::call("ddd:factory {$domain->dotName}:{$modelName}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($domainFactory->path),
    );

    expect(file_exists($expectedFactoryPath))->toBeTrue(
        "Expected factory to be generated in {$expectedFactoryPath}"
    );

    $contents = file_get_contents($expectedFactoryPath);

    expect($contents)
        ->toContain("namespace {$domainFactory->namespace};")
        ->toContain("use {$domainModel->fqn};")
        ->toContain("class {$domainFactory->name} extends Factory")
        ->toContain("protected \$model = {$modelName}::class;");
})->with('domainPaths')->with('domainSubdomain');

it('normalizes factory classes with Factory suffix')->markTestIncomplete();
