<?php

namespace Lunarstorm\LaravelDDD;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Lunarstorm\LaravelDDD\Commands\LaravelDDDCommand;
use Lunarstorm\LaravelDDD\Commands\MakeModel;

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
