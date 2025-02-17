<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can generate objects under deeply nested subdomains', function ($type, $configuredNamespace, $nameInput, $expectedNamespace, $expectedPath) {
    if (in_array($type, ['class', 'enum', 'interface', 'trait'])) {
        skipOnLaravelVersionsBelow('11');
    }

    $domainPath = 'src/Domain';
    $domainRoot = 'Domain';

    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);
    Config::set("ddd.namespaces.{$type}", $configuredNamespace);

    $slug = Str::slug($type);

    $command = "ddd:{$slug} {$nameInput}";

    expect($command)->toGenerateFileWithNamespace($expectedPath, $expectedNamespace);
})->with([
    'model Betterflow.V1.Editorials:Another' => [
        'model',
        'Models',
        'Betterflow.V1.Editorials:Another',
        'Domain\Betterflow\V1\Editorials\Models',
        'src/Domain/Betterflow/V1/Editorials/Models/Another.php'
    ],

    'model Betterflow.V1.Editorials:Nested/Another' => [
        'model',
        'Models',
        'Betterflow.V1.Editorials:Nested/Another',
        'Domain\Betterflow\V1\Editorials\Models\Nested',
        'src/Domain/Betterflow/V1/Editorials/Models/Nested/Another.php'
    ],

    'model Betterflow.V1.Editorials:Nested.Another' => [
        'model',
        'Models',
        'Betterflow.V1.Editorials:Nested.Another',
        'Domain\Betterflow\V1\Editorials\Models\Nested',
        'src/Domain/Betterflow/V1/Editorials/Models/Nested/Another.php'
    ],
]);
