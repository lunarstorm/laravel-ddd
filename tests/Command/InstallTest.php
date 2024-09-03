<?php

use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->setupTestApplication();
});

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
    expect(file_get_contents($path))->toEqual(file_get_contents(__DIR__.'/../../config/ddd.php'));

    unlink($path);
});

it('can initialize composer.json', function ($domainPath, $domainRoot) {
    $originalContents = file_get_contents(base_path('composer.json'));

    $this->updateComposer(
        forget: [
            ['autoload', 'psr-4', 'Domains\\'],
            ['autoload', 'psr-4', 'Domain\\'],
        ]
    );

    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $data = json_decode(file_get_contents(base_path('composer.json')), true);
    $before = data_get($data, ['autoload', 'psr-4', $domainRoot.'\\']);
    expect($before)->toBeNull();

    $command = $this->artisan('ddd:install');
    $command->expectsConfirmation('Would you like to publish stubs?', 'no');
    $command->execute();

    $data = json_decode(file_get_contents(base_path('composer.json')), true);
    $after = data_get($data, ['autoload', 'psr-4', $domainRoot.'\\']);
    expect($after)->toEqual(config('ddd.domain_path'));

    unlink(config_path('ddd.php'));

    // Restore the original composer.json
    file_put_contents(base_path('composer.json'), $originalContents);

    $this->composerReload();
})->with([
    ['src/Domain', 'Domain'],
    ['src/Domains', 'Domains'],
    ['src/CustomDomainRoot', 'CustomDomainRoot'],
]);
