<?php

namespace Lunarstorm\LaravelDDD;

use Lunarstorm\LaravelDDD\Commands\InstallCommand;
use Lunarstorm\LaravelDDD\Commands\MakeAction;
use Lunarstorm\LaravelDDD\Commands\MakeBaseModel;
use Lunarstorm\LaravelDDD\Commands\MakeBaseViewModel;
use Lunarstorm\LaravelDDD\Commands\MakeDTO;
use Lunarstorm\LaravelDDD\Commands\MakeFactory;
use Lunarstorm\LaravelDDD\Commands\MakeModel;
use Lunarstorm\LaravelDDD\Commands\MakeValueObject;
use Lunarstorm\LaravelDDD\Commands\MakeViewModel;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
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
            ->hasConfigFile()
            ->hasCommands([
                InstallCommand::class,
                MakeModel::class,
                MakeFactory::class,
                MakeBaseModel::class,
                MakeDTO::class,
                MakeValueObject::class,
                MakeViewModel::class,
                MakeBaseViewModel::class,
                MakeAction::class,
            ]);
    }

    public function packageBooted()
    {
        $this->publishes([
            $this->package->basePath('/../stubs') => resource_path("stubs/{$this->package->shortName()}"),
        ], "{$this->package->shortName()}-stubs");
    }

    public function packageRegistered()
    {
        (new DomainAutoloader())->autoload();
    }
}
