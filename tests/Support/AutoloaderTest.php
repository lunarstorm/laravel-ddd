<?php

use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

beforeEach(function () {
    $this->setupTestApplication();
});

it('can run', function () {
    $autoloader = new DomainAutoloader;

    $autoloader->autoload();
})->throwsNoExceptions();
