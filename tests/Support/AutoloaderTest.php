<?php

use Lunarstorm\LaravelDDD\Facades\Autoload;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;

beforeEach(function () {
    $this->setupTestApplication();
});

it('can run', function () {
    Autoload::run();
})->throwsNoExceptions();

beforeEach(function () {
    config([
        'ddd.domain_path' => 'src/Domain',
        'ddd.domain_namespace' => 'Domain',
        'ddd.application_namespace' => 'Application',
        'ddd.application_path' => 'src/Application',
        'ddd.application_objects' => [
            'controller',
            'request',
            'middleware',
        ],
        'ddd.layers' => [
            'Infrastructure' => 'src/Infrastructure',
            'Support' => 'src/Support',
            'Library' => 'lib',
        ],
        'ddd.autoload_ignore' => [
            'Tests',
            'Database/Migrations',
        ],
        'cache.default' => 'file',
    ]);

    $this->setupTestApplication();
});

it('can discover paths to all layers', function () {
    $autoloader = app(AutoloadManager::class);

    $expected = [
        app()->basePath('src/Domain'),
        app()->basePath('src/Application'),
        app()->basePath('src/Infrastructure'),
        app()->basePath('src/Support'),
        app()->basePath('lib'),
    ];

    expect($autoloader->getAllLayerPaths())->toEqualCanonicalizing($expected);
});
