<?php

it('[action] prompts for missing input', function () {
    $this->artisan('ddd:action')
        ->expectsQuestion('What should the action be named?', 'DoThatThing')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->assertExitCode(0);
});

it('[view model] prompts for missing input', function () {
    $this->artisan('ddd:view-model')
        ->expectsQuestion('What should the view model be named?', 'Belt')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->assertExitCode(0);
});

it('[base view model] prompts for missing input', function () {
    $this->artisan('ddd:base-view-model')
        ->expectsQuestion('What is the domain?', 'Shared')
        ->assertExitCode(0);
});

it('[model] prompts for missing input', function () {
    $this->artisan('ddd:model')
        ->expectsQuestion('What should the model be named?', 'Belt')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->assertExitCode(0);
});

it('[base model] prompts for missing input', function () {
    $this->artisan('ddd:base-model')
        ->expectsQuestion('What is the domain?', 'Shared')
        ->assertExitCode(0);
});

it('[value object] prompts for missing input', function () {
    $this->artisan('ddd:value')
        ->expectsQuestion('What should the value object be named?', 'Belt')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->assertExitCode(0);
});

it('[data transfer object] prompts for missing input', function () {
    $this->artisan('ddd:dto')
        ->expectsQuestion('What should the data transfer object be named?', 'Belt')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->assertExitCode(0);
});
