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
                \Lunarstorm\LaravelDDD\Commands\DomainCastMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainChannelMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainConsoleMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainEventMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainExceptionMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainJobMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainListenerMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainMailMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainNotificationMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainObserverMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainPolicyMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainProviderMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainResourceMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainRuleMakeCommand::class,
                \Lunarstorm\LaravelDDD\Commands\DomainScopeMakeCommand::class,
            ]);

        // Enum generator only in Laravel 11
        if (app()->version() >= 11) {
            $package->hasCommand(\Lunarstorm\LaravelDDD\Commands\DomainEnumMakeCommand::class);
        }
    }

    public function packageBooted()
    {
        $this->publishes([
            $this->package->basePath('/../stubs') => resource_path("stubs/{$this->package->shortName()}"),
        ], "{$this->package->shortName()}-stubs");
    }
}
