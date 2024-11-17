<?php

use Lunarstorm\LaravelDDD\Support\GeneratorBlueprint;

beforeEach(function () {
    config()->set([
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
            'NestedLayer' => 'src/Nested/Layer',
            'AppNested' => 'app/Nested',
        ],
    ]);
});

it('handles nested objects', function ($nameInput, $normalized) {
    $blueprint = new GeneratorBlueprint(
        commandName: 'ddd:model',
        nameInput: $nameInput,
        domainName: 'SomeDomain',
    );

    expect($blueprint->schema)
        ->name->toBe($normalized)
        ->namespace->toBe('Domain\SomeDomain\Models');
})->with([
    ['Nested\\Thing', 'Nested\\Thing'],
    ['Nested/Thing', 'Nested\\Thing'],
    ['Nested/Thing/Deeply', 'Nested\\Thing\\Deeply'],
    ['Nested\\Thing/Deeply', 'Nested\\Thing\\Deeply'],
]);

it('handles objects in the application layer', function ($command, $domainName, $nameInput, $expectedName, $expectedNamespace, $expectedFqn, $expectedPath) {
    $blueprint = new GeneratorBlueprint(
        commandName: $command,
        nameInput: $nameInput,
        domainName: $domainName,
    );

    expect($blueprint->schema)
        ->name->toBe($expectedName)
        ->namespace->toBe($expectedNamespace)
        ->fullyQualifiedName->toBe($expectedFqn)
        ->path->toEqualPath($expectedPath);
})->with([
    ['ddd:controller', 'SomeDomain', 'ApplicationController', 'ApplicationController', 'Application\\SomeDomain\\Controllers', 'Application\\SomeDomain\\Controllers\\ApplicationController', 'src/Application/SomeDomain/Controllers/ApplicationController.php'],
    ['ddd:controller', 'SomeDomain', 'Application', 'Application', 'Application\\SomeDomain\\Controllers', 'Application\\SomeDomain\\Controllers\\Application', 'src/Application/SomeDomain/Controllers/Application.php'],
    ['ddd:middleware', 'SomeDomain', 'CrazyMiddleware', 'CrazyMiddleware', 'Application\\SomeDomain\\Middleware', 'Application\\SomeDomain\\Middleware\\CrazyMiddleware', 'src/Application/SomeDomain/Middleware/CrazyMiddleware.php'],
    ['ddd:request', 'SomeDomain', 'LazyRequest', 'LazyRequest', 'Application\\SomeDomain\\Requests', 'Application\\SomeDomain\\Requests\\LazyRequest', 'src/Application/SomeDomain/Requests/LazyRequest.php'],
]);

it('handles objects in custom layers', function ($command, $domainName, $nameInput, $expectedName, $expectedNamespace, $expectedFqn, $expectedPath) {
    $blueprint = new GeneratorBlueprint(
        commandName: $command,
        nameInput: $nameInput,
        domainName: $domainName,
    );

    expect($blueprint->schema)
        ->name->toBe($expectedName)
        ->namespace->toBe($expectedNamespace)
        ->fullyQualifiedName->toBe($expectedFqn)
        ->path->toEqualPath($expectedPath);
})->with([
    ['ddd:model', 'Infrastructure', 'System', 'System', 'Infrastructure\\Models', 'Infrastructure\\Models\\System', 'src/Infrastructure/Models/System.php'],
    ['ddd:factory', 'Infrastructure', 'System', 'SystemFactory', 'Infrastructure\\Database\\Factories', 'Infrastructure\\Database\\Factories\\SystemFactory', 'src/Infrastructure/Database/Factories/SystemFactory.php'],
    ['ddd:provider', 'Infrastructure', 'InfrastructureServiceProvider', 'InfrastructureServiceProvider', 'Infrastructure\\Providers', 'Infrastructure\\Providers\\InfrastructureServiceProvider', 'src/Infrastructure/Providers/InfrastructureServiceProvider.php'],
    ['ddd:provider', 'Infrastructure', 'Infrastructure\\InfrastructureServiceProvider', 'Infrastructure\\InfrastructureServiceProvider', 'Infrastructure\\Providers', 'Infrastructure\\Providers\\Infrastructure\\InfrastructureServiceProvider', 'src/Infrastructure/Providers/Infrastructure/InfrastructureServiceProvider.php'],
    ['ddd:provider', 'Infrastructure', 'InfrastructureServiceProvider', 'InfrastructureServiceProvider', 'Infrastructure\\Providers', 'Infrastructure\\Providers\\InfrastructureServiceProvider', 'src/Infrastructure/Providers/InfrastructureServiceProvider.php'],
    ['ddd:provider', 'AppNested', 'CrazyServiceProvider', 'CrazyServiceProvider', 'AppNested\\Providers', 'AppNested\\Providers\\CrazyServiceProvider', 'app/Nested/Providers/CrazyServiceProvider.php'],
    ['ddd:provider', 'NestedLayer', 'CrazyServiceProvider', 'CrazyServiceProvider', 'NestedLayer\\Providers', 'NestedLayer\\Providers\\CrazyServiceProvider', 'src/Nested/Layer/Providers/CrazyServiceProvider.php'],
]);

it('handles objects whose name contains the domain name', function ($command, $domainName, $nameInput, $expectedName, $expectedNamespace, $expectedFqn, $expectedPath) {
    $blueprint = new GeneratorBlueprint(
        commandName: $command,
        nameInput: $nameInput,
        domainName: $domainName,
    );

    expect($blueprint->schema)
        ->name->toBe($expectedName)
        ->namespace->toBe($expectedNamespace)
        ->fullyQualifiedName->toBe($expectedFqn)
        ->path->toEqualPath($expectedPath);
})->with([
    ['ddd:model', 'SomeDomain', 'SomeDomain', 'SomeDomain', 'Domain\\SomeDomain\\Models', 'Domain\\SomeDomain\\Models\\SomeDomain', 'src/Domain/SomeDomain/Models/SomeDomain.php'],
    ['ddd:model', 'SomeDomain', 'SomeDomainModel', 'SomeDomainModel', 'Domain\\SomeDomain\\Models', 'Domain\\SomeDomain\\Models\\SomeDomainModel', 'src/Domain/SomeDomain/Models/SomeDomainModel.php'],
    ['ddd:model', 'SomeDomain', 'Nested\\SomeDomain', 'Nested\\SomeDomain', 'Domain\\SomeDomain\\Models', 'Domain\\SomeDomain\\Models\\Nested\\SomeDomain', 'src/Domain/SomeDomain/Models/Nested/SomeDomain.php'],
    ['ddd:model', 'SomeDomain', 'SomeDomain\\SomeDomain', 'SomeDomain\\SomeDomain', 'Domain\\SomeDomain\\Models', 'Domain\\SomeDomain\\Models\\SomeDomain\\SomeDomain', 'src/Domain/SomeDomain/Models/SomeDomain/SomeDomain.php'],
    ['ddd:model', 'SomeDomain', 'SomeDomain\\SomeDomainModel', 'SomeDomain\\SomeDomainModel', 'Domain\\SomeDomain\\Models', 'Domain\\SomeDomain\\Models\\SomeDomain\\SomeDomainModel', 'src/Domain/SomeDomain/Models/SomeDomain/SomeDomainModel.php'],
    ['ddd:model', 'Infrastructure', 'Infrastructure', 'Infrastructure', 'Infrastructure\\Models', 'Infrastructure\\Models\\Infrastructure', 'src/Infrastructure/Models/Infrastructure.php'],
    ['ddd:model', 'Infrastructure', 'Nested\\Infrastructure', 'Nested\\Infrastructure', 'Infrastructure\\Models', 'Infrastructure\\Models\\Nested\\Infrastructure', 'src/Infrastructure/Models/Nested/Infrastructure.php'],
    ['ddd:controller', 'SomeDomain', 'SomeDomain', 'SomeDomain', 'Application\\SomeDomain\\Controllers', 'Application\\SomeDomain\\Controllers\\SomeDomain', 'src/Application/SomeDomain/Controllers/SomeDomain.php'],
    ['ddd:controller', 'SomeDomain', 'SomeDomainController', 'SomeDomainController', 'Application\\SomeDomain\\Controllers', 'Application\\SomeDomain\\Controllers\\SomeDomainController', 'src/Application/SomeDomain/Controllers/SomeDomainController.php'],
    ['ddd:controller', 'SomeDomain', 'SomeDomain\\SomeDomain', 'SomeDomain\\SomeDomain', 'Application\\SomeDomain\\Controllers', 'Application\\SomeDomain\\Controllers\\SomeDomain\\SomeDomain', 'src/Application/SomeDomain/Controllers/SomeDomain/SomeDomain.php'],
]);

it('handles absolute-path names', function ($command, $domainName, $nameInput, $expectedName, $expectedNamespace, $expectedFqn, $expectedPath) {
    $blueprint = new GeneratorBlueprint(
        commandName: $command,
        nameInput: $nameInput,
        domainName: $domainName,
    );

    expect($blueprint->schema)
        ->name->toBe($expectedName)
        ->namespace->toBe($expectedNamespace)
        ->fullyQualifiedName->toBe($expectedFqn)
        ->path->toEqualPath($expectedPath);
})->with([
    ['ddd:model', 'SomeDomain', '/RootModel', 'RootModel', 'Domain\\SomeDomain', 'Domain\\SomeDomain\\RootModel', 'src/Domain/SomeDomain/RootModel.php'],
    ['ddd:model', 'SomeDomain', '/CustomLocation/Thing', 'CustomLocation\\Thing', 'Domain\\SomeDomain', 'Domain\\SomeDomain\\CustomLocation\\Thing', 'src/Domain/SomeDomain/CustomLocation/Thing.php'],
    ['ddd:model', 'SomeDomain', '/Custom/Nested/Thing', 'Custom\\Nested\\Thing', 'Domain\\SomeDomain', 'Domain\\SomeDomain\\Custom\\Nested\\Thing', 'src/Domain/SomeDomain/Custom/Nested/Thing.php'],
]);
