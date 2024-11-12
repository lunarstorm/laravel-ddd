<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\ValueObjects\CommandContext;
use Lunarstorm\LaravelDDD\ValueObjects\ObjectSchema;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    $this->setupTestApplication();
});

it('can register a custom namespace resolver', function () {
    Config::set('ddd.application', [
        'path' => 'src/App',
        'namespace' => 'App',
    ]);

    DDD::resolveObjectSchemaUsing(function (string $domainName, string $nameInput, string $type, CommandContext $command): ?ObjectSchema {
        if ($type === 'controller' && $command->option('api')) {
            return new ObjectSchema(
                name: $name = str($nameInput)->replaceEnd('Controller', '')->finish('ApiController')->toString(),
                namespace: "App\\Api\\Controllers\\{$domainName}",
                fullyQualifiedName: "App\\Api\\Controllers\\{$domainName}\\{$name}",
                path: "src/App/Api/Controllers/{$domainName}/{$name}.php",
            );
        }

        return null;
    });

    Artisan::call('ddd:controller', [
        'name' => 'PaymentController',
        '--domain' => 'Invoicing',
        '--api' => true,
    ]);

    $output = Artisan::output();

    expect($output)
        ->toContainFilepath('src/App/Api/Controllers/Invoicing/PaymentApiController.php');

    $expectedPath = base_path('src/App/Api/Controllers/Invoicing/PaymentApiController.php');

    expect(file_get_contents($expectedPath))
        ->toContain("namespace App\Api\Controllers\Invoicing;");
});
