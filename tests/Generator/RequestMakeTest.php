<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    Config::set('ddd.application', [
        'path' => 'app/Modules',
        'namespace' => 'App\Modules',
        'objects' => ['controller', 'request'],
    ]);

    $this->setupTestApplication();
});

it('can generate domain request', function ($domainName, $requestName, $relativePath, $expectedNamespace) {
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:request {$domainName}:{$requestName}");

    expect($output = Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))
        ->toContain("namespace {$expectedNamespace};");
})->with([
    'Invoicing:StoreInvoiceRequest' => [
        'Invoicing',
        'StoreInvoiceRequest',
        'app/Modules/Invoicing/Requests/StoreInvoiceRequest.php',
        'App\Modules\Invoicing\Requests',
    ],

    'Reporting.Internal:UpdateReportRequest' => [
        'Reporting.Internal',
        'UpdateReportRequest',
        'app/Modules/Reporting/Internal/Requests/UpdateReportRequest.php',
        'App\Modules\Reporting\Internal\Requests',
    ],
]);
