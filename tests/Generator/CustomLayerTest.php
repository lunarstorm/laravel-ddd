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
    'action' => ['action', 'CustomLayerAction', 'CustomLayer\Actions', 'src/CustomLayer/Actions/CustomLayerAction.php'],
    'cast' => ['cast', 'CustomLayerCast', 'CustomLayer\Casts', 'src/CustomLayer/Casts/CustomLayerCast.php'],
    'channel' => ['channel', 'CustomLayerChannel', 'CustomLayer\Channels', 'src/CustomLayer/Channels/CustomLayerChannel.php'],
    'command' => ['command', 'CustomLayerCommand', 'CustomLayer\Commands', 'src/CustomLayer/Commands/CustomLayerCommand.php'],
    'event' => ['event', 'CustomLayerEvent', 'CustomLayer\Events', 'src/CustomLayer/Events/CustomLayerEvent.php'],
    'exception' => ['exception', 'CustomLayerException', 'CustomLayer\Exceptions', 'src/CustomLayer/Exceptions/CustomLayerException.php'],
    'job' => ['job', 'CustomLayerJob', 'CustomLayer\Jobs', 'src/CustomLayer/Jobs/CustomLayerJob.php'],
    'listener' => ['listener', 'CustomLayerListener', 'CustomLayer\Listeners', 'src/CustomLayer/Listeners/CustomLayerListener.php'],
    'mail' => ['mail', 'CustomLayerMail', 'CustomLayer\Mail', 'src/CustomLayer/Mail/CustomLayerMail.php'],
    'notification' => ['notification', 'CustomLayerNotification', 'CustomLayer\Notifications', 'src/CustomLayer/Notifications/CustomLayerNotification.php'],
    'observer' => ['observer', 'CustomLayerObserver', 'CustomLayer\Observers', 'src/CustomLayer/Observers/CustomLayerObserver.php'],
    'policy' => ['policy', 'CustomLayerPolicy', 'CustomLayer\Policies', 'src/CustomLayer/Policies/CustomLayerPolicy.php'],
    'provider' => ['provider', 'CustomLayerServiceProvider', 'CustomLayer\Providers', 'src/CustomLayer/Providers/CustomLayerServiceProvider.php'],
    'resource' => ['resource', 'CustomLayerResource', 'CustomLayer\Resources', 'src/CustomLayer/Resources/CustomLayerResource.php'],
    'rule' => ['rule', 'CustomLayerRule', 'CustomLayer\Rules', 'src/CustomLayer/Rules/CustomLayerRule.php'],
    'scope' => ['scope', 'CustomLayerScope', 'CustomLayer\Scopes', 'src/CustomLayer/Scopes/CustomLayerScope.php'],
    'seeder' => ['seeder', 'CustomLayerSeeder', 'CustomLayer\Database\Seeders', 'src/CustomLayer/Database/Seeders/CustomLayerSeeder.php'],
    'class' => ['class', 'CustomLayerClass', 'CustomLayer', 'src/CustomLayer/CustomLayerClass.php'],
    'enum' => ['enum', 'CustomLayerEnum', 'CustomLayer\Enums', 'src/CustomLayer/Enums/CustomLayerEnum.php'],
    'interface' => ['interface', 'CustomLayerInterface', 'CustomLayer', 'src/CustomLayer/CustomLayerInterface.php'],
    'trait' => ['trait', 'CustomLayerTrait', 'CustomLayer', 'src/CustomLayer/CustomLayerTrait.php'],
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
    'request' => ['request', 'CustomLayerRequest', 'Application\CustomLayer\Requests', 'src/Application/CustomLayer/Requests/CustomLayerRequest.php'],
    'controller' => ['controller', 'CustomLayerController', 'Application\CustomLayer\Controllers', 'src/Application/CustomLayer/Controllers/CustomLayerController.php'],
    'middleware' => ['middleware', 'CustomLayerMiddleware', 'Application\CustomLayer\Middleware', 'src/Application/CustomLayer/Middleware/CustomLayerMiddleware.php'],
]);
