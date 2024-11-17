<?php

use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\Path;

beforeEach(function () {
    $this->setupTestApplication();
});

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

    $this->expectedPaths = collect([
        app()->basePath('src/Domain'),
        app()->basePath('src/Application'),
        app()->basePath('src/Infrastructure'),
        app()->basePath('src/Support'),
        app()->basePath('lib'),
    ])->map(fn ($path) => Path::normalize($path))->toArray();

    $this->setupTestApplication();
});

it('can discover paths to all layers', function () {
    $autoloader = app(AutoloadManager::class);

    expect($autoloader->getAllLayerPaths())->toEqualCanonicalizing($this->expectedPaths);
});
