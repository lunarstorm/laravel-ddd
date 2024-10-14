<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Support\DomainMigration;
use Lunarstorm\LaravelDDD\Support\Path;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

beforeEach(function () {
    config([
        'ddd.autoload.migrations' => true,
    ]);

    DomainCache::clear();
});

it('can generate domain migrations', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $domain = 'Invoicing';

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.migration'),
    ]);

    $migrationFolder = base_path(Path::normalize($relativePath));

    $filesBefore = glob("{$migrationFolder}/*");

    expect(count($filesBefore))->toBe(0);

    Artisan::call("ddd:migration {$domain}:CreateInvoicesTable");

    expect($output = Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output
            ->toContainFilepath($relativePath)
            ->toContain('_create_invoices_table.php'),
    );

    $filesAfter = glob("{$migrationFolder}/*");

    $createdMigrationFile = Arr::last($filesAfter);

    expect($createdMigrationFile)->toEndWith('_create_invoices_table.php');

    expect(file_get_contents($createdMigrationFile))
        ->toContain('return new class extends Migration');
})->with('domainPaths');

it('discovers domain migration folders', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $discoveredPaths = DomainMigration::discoverPaths();

    expect($discoveredPaths)->toHaveCount(0);

    Artisan::call('ddd:migration Invoicing:'.uniqid('migration'));
    Artisan::call('ddd:migration Shared:'.uniqid('migration'));
    Artisan::call('ddd:migration Reporting:'.uniqid('migration'));
    Artisan::call('ddd:migration Reporting:'.uniqid('migration'));
    Artisan::call('ddd:migration Reporting:'.uniqid('migration'));

    $discoveredPaths = DomainMigration::discoverPaths();

    expect($discoveredPaths)->toHaveCount(3);

    $expectedFolderPatterns = [
        'Invoicing/Database/Migrations',
        'Shared/Database/Migrations',
        'Reporting/Database/Migrations',
    ];

    foreach ($discoveredPaths as $path) {
        expect(str($path)->contains($expectedFolderPatterns))
            ->toBeTrue('Expecting path to contain one of the expected folder patterns');
    }
})->with('domainPaths');
