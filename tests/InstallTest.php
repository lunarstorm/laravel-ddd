<?php

use Illuminate\Support\Facades\Artisan;

it('publishes config', function () {
    $path = config_path('ddd.php');

    if (file_exists($path)) {
        unlink($path);
    }

    expect(file_exists($path))->toBeFalse();

    $command = $this->artisan('ddd:install');
    $command->expectsOutput('Publishing config...');
    $command->expectsOutput('Ensuring domain path is registered in composer.json...');
    $command->expectsConfirmation('Would you like to publish stubs?', 'no');
    $command->execute();

    expect(file_exists($path))->toBeTrue();
    expect(file_get_contents($path))
        ->toEqual(file_get_contents(__DIR__ . '/../config/ddd.php'));

    unlink($path);
});

it('initializes composer.json', function () {
    $data = json_decode(file_get_contents(base_path('composer.json')), true);
    $before = data_get($data, ['autoload', 'psr-4', 'Domains\\']);
    expect($before)->toBeNull();

    $command = $this->artisan('ddd:install');
    $command->expectsConfirmation('Would you like to publish stubs?', 'no');
    $command->execute();

    $data = json_decode(file_get_contents(base_path('composer.json')), true);
    $after = data_get($data, ['autoload', 'psr-4', 'Domains\\']);
    expect($after)->toEqual(config('ddd.paths.domains'));

    unlink(config_path('ddd.php'));
});
