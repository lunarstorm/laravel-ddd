<?php

namespace Lunarstorm\LaravelDDD;

use Lunarstorm\LaravelDDD\Commands\LaravelDDDCommand;
use Lunarstorm\LaravelDDD\Commands\MakeModel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDDDServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-ddd')
            ->hasConfigFile('ddd')
            ->hasCommands([
                LaravelDDDCommand::class,
                MakeModel::class,
            ]);
    }
}
