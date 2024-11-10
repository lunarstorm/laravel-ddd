<?php

use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

use function PHPUnit\Framework\assertDirectoryDoesNotExist;
use function PHPUnit\Framework\assertDirectoryExists;

beforeEach(function () {
    $this->cleanSlate();

    $publishedStubFolder = app()->basePath('stubs/ddd');

    File::deleteDirectory($publishedStubFolder);

    assertDirectoryDoesNotExist($publishedStubFolder);
});

afterEach(function () {
    $this->cleanSlate();
});

it('can publish all stubs using --all option', function () {
    $this
        ->artisan('ddd:stub --all')
        ->doesntExpectOutput('Publishing stubs...')
        ->assertSuccessful()
        ->execute();

    $publishedStubFolder = app()->basePath('stubs/ddd');

    assertDirectoryExists($publishedStubFolder);

    $stubFiles = File::files($publishedStubFolder);

    expect(count($stubFiles))->toBeGreaterThan(0);

    expect(count($stubFiles))->toEqual(count([
        ...app('ddd')->stubs()->dddStubs(),
        ...app('ddd')->stubs()->frameworkStubs(),
    ]));
});

it('can publish all stubs interactively', function () {
    $path = app()->configPath('ddd.php');
    $publishedStubFolder = app()->basePath('stubs/ddd');

    $this
        ->artisan('ddd:stub')
        ->expectsQuestion('What do you want to do?', 'all')
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeFalse();

    assertDirectoryExists($publishedStubFolder);

    $stubFiles = File::files($publishedStubFolder);

    expect(count($stubFiles))->toBeGreaterThan(0);

    expect(count($stubFiles))->toEqual(count([
        ...app('ddd')->stubs()->dddStubs(),
        ...app('ddd')->stubs()->frameworkStubs(),
    ]));
});

it('can publish specific stubs using arguments', function ($stubsToPublish) {
    $expectedStubFilenames = collect($stubsToPublish)
        ->map(fn ($stub) => $stub.'.stub')
        ->all();

    $arguments = collect($stubsToPublish)->join(' ');

    $this
        ->artisan("ddd:stub {$arguments}")
        ->assertSuccessful()
        ->execute();

    $publishedStubFolder = app()->basePath('stubs/ddd');

    assertDirectoryExists($publishedStubFolder);

    $stubFiles = File::files($publishedStubFolder);

    expect(count($stubFiles))->toEqual(count($stubsToPublish));

    foreach ($stubFiles as $file) {
        expect($file->getFilename())->toBeIn($expectedStubFilenames);
    }
})->with([
    'model' => [['model']],
    'model/action/dto' => [['model', 'action', 'dto']],
    'model/model.pivot' => [['model', 'model.pivot']],
    'controller' => [['controller']],
]);

it('can publish specific stubs interactively', function () {
    $publishedStubFolder = app()->basePath('stubs/ddd');

    assertDirectoryDoesNotExist($publishedStubFolder);

    $options = app('ddd')->stubs()->allStubs();

    $matches = collect($options)
        ->filter(fn ($stub, $path) => str($stub)->contains('model'))
        ->all();

    $this
        ->artisan('ddd:stub')
        ->expectsQuestion('What do you want to do?', 'some')
        ->expectsSearch(
            'Which stub should be published?',
            search: 'model',
            answers: $matches,
            answer: ['model.stub']
        )
        ->assertSuccessful()
        ->execute();

    assertDirectoryExists($publishedStubFolder);

    $stubFiles = File::files($publishedStubFolder);

    expect(count($stubFiles))->toEqual(1);

    expect($stubFiles[0]->getFilename())->toEqual('model.stub');
})->skip(fn () => Feature::PromptMultiSearchAssertion->missing(), 'Multi-search assertion not available');
