<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\Path;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');
});

// it('can generate a domain model with factory', function () {
//     $domainName = 'Invoicing';
//     $modelName = 'Record';

//     $domain = new Domain($domainName);

//     $factoryName = "{$modelName}Factory";

//     $domainModel = $domain->model($modelName);

//     $domainFactory = $domain->factory($factoryName);

//     $expectedModelPath = base_path($domainModel->path);

//     if (file_exists($expectedModelPath)) {
//         unlink($expectedModelPath);
//     }

//     $expectedFactoryPath = base_path($domainFactory->path);

//     if (file_exists($expectedFactoryPath)) {
//         unlink($expectedFactoryPath);
//     }

//     Artisan::call('ddd:model', [
//         'name' => $modelName,
//         '--domain' => $domain->dotName,
//         '--factory' => true,
//     ]);

//     $output = Artisan::output();

//     expect($output)->toContainFilepath($domainModel->path);

//     expect(file_exists($expectedModelPath))->toBeTrue("Expecting model file to be generated at {$expectedModelPath}");
//     expect(file_exists($expectedFactoryPath))->toBeTrue("Expecting factory file to be generated at {$expectedFactoryPath}");

//     expect(file_get_contents($expectedFactoryPath))
//         ->toContain("use {$domainModel->fullyQualifiedName};")
//         ->toContain("protected \$model = {$modelName}::class;");
// });

it('can generate domain model with options', function ($options, $objectType, $objectName, $expectedObjectPath) {
    $domainName = 'Invoicing';
    $modelName = 'Record';

    $domain = new Domain($domainName);

    $domainModel = $domain->model($modelName);

    $expectedModelPath = base_path($domainModel->path);

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    if (file_exists($expectedObjectPath)) {
        unlink($expectedObjectPath);
    }

    $command = [
        'ddd:model',
        [
            'name' => $modelName,
            '--domain' => $domain->dotName,
            ...$options,
        ],
    ];

    $this->artisan(...$command)
        ->expectsOutputToContain(Path::normalize($domainModel->path))
        ->assertExitCode(0);

    $path = base_path($expectedObjectPath);

    expect(file_exists($path))->toBeTrue("Expecting {$objectType} to be generated at {$path}");
})->with([
    '--factory' => [
        ['--factory' => true],
        'factory',
        'RecordFactory',
        'src/Domain/Invoicing/Database/Factories/RecordFactory.php',
    ],

    '--seed' => [
        ['--seed' => true],
        'seeder',
        'RecordSeeder',
        'src/Domain/Invoicing/Database/Seeders/RecordSeeder.php',
    ],

    '--policy' => [
        ['--policy' => true],
        'policy',
        'RecordPolicy',
        'src/Domain/Invoicing/Policies/RecordPolicy.php',
    ],
]);
