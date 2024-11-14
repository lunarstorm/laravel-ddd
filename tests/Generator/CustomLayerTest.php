<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->cleanSlate();

    Config::set('ddd.layers', [
        'CustomLayer' => 'src/CustomLayer',
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

    $command = "ddd:{$type} CustomLayer:{$objectName}";

    Artisan::call($command);

    expect(Artisan::output())->toContainFilepath($relativePath);

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with([
    'action' => ['action', 'SomeAction', 'CustomLayer\Actions', 'src/CustomLayer/Actions/SomeAction.php'],
    'cast' => ['cast', 'SomeCast', 'CustomLayer\Casts', 'src/CustomLayer/Casts/SomeCast.php'],
    'channel' => ['channel', 'SomeChannel', 'CustomLayer\Channels', 'src/CustomLayer/Channels/SomeChannel.php'],
    'command' => ['command', 'SomeCommand', 'CustomLayer\Commands', 'src/CustomLayer/Commands/SomeCommand.php'],
    'event' => ['event', 'SomeEvent', 'CustomLayer\Events', 'src/CustomLayer/Events/SomeEvent.php'],
    'exception' => ['exception', 'SomeException', 'CustomLayer\Exceptions', 'src/CustomLayer/Exceptions/SomeException.php'],
    'job' => ['job', 'SomeJob', 'CustomLayer\Jobs', 'src/CustomLayer/Jobs/SomeJob.php'],
    'listener' => ['listener', 'SomeListener', 'CustomLayer\Listeners', 'src/CustomLayer/Listeners/SomeListener.php'],
    'mail' => ['mail', 'SomeMail', 'CustomLayer\Mail', 'src/CustomLayer/Mail/SomeMail.php'],
    'notification' => ['notification', 'SomeNotification', 'CustomLayer\Notifications', 'src/CustomLayer/Notifications/SomeNotification.php'],
    'observer' => ['observer', 'SomeObserver', 'CustomLayer\Observers', 'src/CustomLayer/Observers/SomeObserver.php'],
    'policy' => ['policy', 'SomePolicy', 'CustomLayer\Policies', 'src/CustomLayer/Policies/SomePolicy.php'],
    'provider' => ['provider', 'SomeProvider', 'CustomLayer\Providers', 'src/CustomLayer/Providers/SomeProvider.php'],
    'resource' => ['resource', 'SomeResource', 'CustomLayer\Resources', 'src/CustomLayer/Resources/SomeResource.php'],
    'rule' => ['rule', 'SomeRule', 'CustomLayer\Rules', 'src/CustomLayer/Rules/SomeRule.php'],
    'scope' => ['scope', 'SomeScope', 'CustomLayer\Scopes', 'src/CustomLayer/Scopes/SomeScope.php'],
    'seeder' => ['seeder', 'SomeSeeder', 'CustomLayer\Database\Seeders', 'src/CustomLayer/Database/Seeders/SomeSeeder.php'],
    'class' => ['class', 'SomeClass', 'CustomLayer', 'src/CustomLayer/SomeClass.php'],
    'enum' => ['enum', 'SomeEnum', 'CustomLayer\Enums', 'src/CustomLayer/Enums/SomeEnum.php'],
    'interface' => ['interface', 'SomeInterface', 'CustomLayer', 'src/CustomLayer/SomeInterface.php'],
    'trait' => ['trait', 'SomeTrait', 'CustomLayer', 'src/CustomLayer/SomeTrait.php'],
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

    $command = "ddd:{$type} CustomLayer:{$objectName}";

    Artisan::call($command);

    expect(Artisan::output())->toContainFilepath($relativePath);

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with([
    'request' => ['request', 'SomeRequest', 'Application\CustomLayer\Requests', 'src/Application/CustomLayer/Requests/SomeRequest.php'],
    'controller' => ['controller', 'SomeController', 'Application\CustomLayer\Controllers', 'src/Application/CustomLayer/Controllers/SomeController.php'],
    'middleware' => ['middleware', 'SomeMiddleware', 'Application\CustomLayer\Middleware', 'src/Application/CustomLayer/Middleware/SomeMiddleware.php'],
]);
