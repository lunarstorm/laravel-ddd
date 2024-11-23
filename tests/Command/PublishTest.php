<?php

use Illuminate\Support\Facades\File;

use function PHPUnit\Framework\assertDirectoryDoesNotExist;
use function PHPUnit\Framework\assertDirectoryExists;

beforeEach(function () {
    $this->cleanSlate();

    $path = app()->configPath('ddd.php');

    if (file_exists($path)) {
        unlink($path);
    }

    expect(file_exists($path))->toBeFalse();

    $publishedStubFolder = app()->basePath('stubs/ddd');

    File::deleteDirectory($publishedStubFolder);

    assertDirectoryDoesNotExist($publishedStubFolder);
});

afterEach(function () {
    $this->cleanSlate();
});

it('can publish config using --config option', function () {
    $path = app()->configPath('ddd.php');

    $this
        ->artisan('ddd:publish --config')
        ->expectsOutputToContain('Publishing config...')
        ->doesntExpectOutput('Publishing stubs...')
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeTrue();
});

it('can publish everything', function ($options) {
    $path = app()->configPath('ddd.php');
    $publishedStubFolder = app()->basePath('stubs/ddd');

    $this
        ->artisan('ddd:publish', $options)
        ->expectsOutputToContain('Publishing config...')
        ->expectsOutputToContain('Publishing stubs...')
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeTrue();

    assertDirectoryExists($publishedStubFolder);

    $stubFiles = File::files($publishedStubFolder);

    expect(count($stubFiles))->toBeGreaterThan(0);
})->with([
    '--all' => [['--all' => true]],
    '--config --stubs' => [['--config' => true, '--stubs' => true]],
]);

it('can publish config interactively', function () {
    $path = app()->configPath('ddd.php');

    $this
        ->artisan('ddd:publish')
        ->expectsQuestion('What should be published?', ['config'])
        ->expectsOutputToContain('Publishing config...')
        ->doesntExpectOutput('Publishing stubs...')
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeTrue();
});

it('can publish stubs interactively', function () {
    $path = app()->configPath('ddd.php');
    $publishedStubFolder = app()->basePath('stubs/ddd');

    $this
        ->artisan('ddd:publish')
        ->expectsQuestion('What should be published?', ['stubs'])
        ->expectsOutputToContain('Publishing stubs...')
        ->doesntExpectOutput('Publishing config...')
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeFalse();

    assertDirectoryExists($publishedStubFolder);
});

it('can publish everything interactively', function () {
    $path = app()->configPath('ddd.php');
    $publishedStubFolder = app()->basePath('stubs/ddd');

    $this
        ->artisan('ddd:publish')
        ->expectsQuestion('What should be published?', ['config', 'stubs'])
        ->expectsOutputToContain('Publishing config...')
        ->expectsOutputToContain('Publishing stubs...')
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeTrue();

    assertDirectoryExists($publishedStubFolder);

    $stubFiles = File::files($publishedStubFolder);

    expect(count($stubFiles))->toBeGreaterThan(0);
});
