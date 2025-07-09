<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Lunarstorm\LaravelDDD\Events\DomainMigrationsPruned;
use Lunarstorm\LaravelDDD\Support\Path;

it('prunes domain migrations when schema:dump --prune is called', function () {
    $this->setupTestApplication();

    Event::fake([DomainMigrationsPruned::class]);

    $migrationFile = app()->basePath('src/Domain/Invoicing/Database/Migrations/2024_10_14_215911_do_nothing.php');

    expect($migrationFile)->toBeFile();

    Artisan::call('schema:dump', [
        '--prune' => true,
    ]);

    Event::assertDispatched(DomainMigrationsPruned::class, function (DomainMigrationsPruned $event) {
        return Path::normalize($event->path) === Path::normalize(app()->basePath('src/Domain/Invoicing/Database/Migrations'));
    });

    expect($migrationFile)->not->toBeFile();
})->skipOnWindows();
