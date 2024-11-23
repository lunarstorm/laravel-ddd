<?php

use Illuminate\Support\Facades\Route;
use Lunarstorm\LaravelDDD\Facades\Autoload;

Route::prefix('laravel-ddd')
    ->middleware(['web'])
    ->as('ddd.')
    ->group(function () {
        Route::get('/', function () {
            return response('home');
        })->name('home');

        Route::get('/config', function () {
            return response(config('ddd'));
        })->name('config');

        Route::get('/autoload', function () {
            return response([
                'providers' => Autoload::getRegisteredProviders(),
                'commands' => Autoload::getRegisteredCommands(),
            ]);
        })->name('autoload');
    });
