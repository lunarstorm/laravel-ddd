<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->cleanSlate();

    Config::set('ddd.layers', [
        'Infrastructure' => 'src/Infrastructure',
    ]);
});

it('can generate objects into custom layers', function ($type, $objectName, $expectedNamespace, $expectedPath) {
    if (in_array($type, ['class', 'enum', 'interface', 'trait'])) {
        skipOnLaravelVersionsBelow('11');
    }

    $relativePath = $expectedPath;
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    $command = "ddd:{$type} Infrastructure:{$objectName}";

    Artisan::call($command);

    expect(Artisan::output())->toContainFilepath($relativePath);

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with([
    'action' => ['action', 'SomeAction', 'Infrastructure\Actions', 'src/Infrastructure/Actions/SomeAction.php'],
    'cast' => ['cast', 'SomeCast', 'Infrastructure\Casts', 'src/Infrastructure/Casts/SomeCast.php'],
    'channel' => ['channel', 'SomeChannel', 'Infrastructure\Channels', 'src/Infrastructure/Channels/SomeChannel.php'],
    'command' => ['command', 'SomeCommand', 'Infrastructure\Commands', 'src/Infrastructure/Commands/SomeCommand.php'],
    'event' => ['event', 'SomeEvent', 'Infrastructure\Events', 'src/Infrastructure/Events/SomeEvent.php'],
    'exception' => ['exception', 'SomeException', 'Infrastructure\Exceptions', 'src/Infrastructure/Exceptions/SomeException.php'],
    'job' => ['job', 'SomeJob', 'Infrastructure\Jobs', 'src/Infrastructure/Jobs/SomeJob.php'],
    'listener' => ['listener', 'SomeListener', 'Infrastructure\Listeners', 'src/Infrastructure/Listeners/SomeListener.php'],
    'mail' => ['mail', 'SomeMail', 'Infrastructure\Mail', 'src/Infrastructure/Mail/SomeMail.php'],
    'notification' => ['notification', 'SomeNotification', 'Infrastructure\Notifications', 'src/Infrastructure/Notifications/SomeNotification.php'],
    'observer' => ['observer', 'SomeObserver', 'Infrastructure\Observers', 'src/Infrastructure/Observers/SomeObserver.php'],
    'policy' => ['policy', 'SomePolicy', 'Infrastructure\Policies', 'src/Infrastructure/Policies/SomePolicy.php'],
    'provider' => ['provider', 'SomeProvider', 'Infrastructure\Providers', 'src/Infrastructure/Providers/SomeProvider.php'],
    'resource' => ['resource', 'SomeResource', 'Infrastructure\Resources', 'src/Infrastructure/Resources/SomeResource.php'],
    'rule' => ['rule', 'SomeRule', 'Infrastructure\Rules', 'src/Infrastructure/Rules/SomeRule.php'],
    'scope' => ['scope', 'SomeScope', 'Infrastructure\Scopes', 'src/Infrastructure/Scopes/SomeScope.php'],
    'seeder' => ['seeder', 'SomeSeeder', 'Infrastructure\Database\Seeders', 'src/Infrastructure/Database/Seeders/SomeSeeder.php'],
    'class' => ['class', 'SomeClass', 'Infrastructure', 'src/Infrastructure/SomeClass.php'],
    'enum' => ['enum', 'SomeEnum', 'Infrastructure\Enums', 'src/Infrastructure/Enums/SomeEnum.php'],
    'interface' => ['interface', 'SomeInterface', 'Infrastructure', 'src/Infrastructure/SomeInterface.php'],
    'trait' => ['trait', 'SomeTrait', 'Infrastructure', 'src/Infrastructure/SomeTrait.php'],
]);

it('ignores custom layer if object belongs in the application layer', function ($type, $objectName, $expectedNamespace, $expectedPath) {
    Config::set([
        'ddd.application_namespace' => 'Application',
        'ddd.application_path' => 'src/Application',
        'ddd.application_objects' => [
            $type,
        ],
    ]);

    $relativePath = $expectedPath;
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    $command = "ddd:{$type} Infrastructure:{$objectName}";

    Artisan::call($command);

    expect(Artisan::output())->toContainFilepath($relativePath);

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with([
    'request' => ['request', 'SomeRequest', 'Application\Infrastructure\Requests', 'src/Application/Infrastructure/Requests/SomeRequest.php'],
    'controller' => ['controller', 'SomeController', 'Application\Infrastructure\Controllers', 'src/Application/Infrastructure/Controllers/SomeController.php'],
    'middleware' => ['middleware', 'SomeMiddleware', 'Application\Infrastructure\Middleware', 'src/Application/Infrastructure/Middleware/SomeMiddleware.php'],
]);
