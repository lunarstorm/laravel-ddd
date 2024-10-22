<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\ValueObjects\DomainCommandContext;

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

    DDD::resolveNamespaceUsing(function (string $domain, string $type, ?DomainCommandContext $context): ?string {
        if ($type == 'controller' && $context->option('api')) {
            return "App\\Api\\Controllers\\{$domain}";
        }

        return null;
    });

    Artisan::call('ddd:controller', [
        'name' => 'PaymentApiController',
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
