<?php

namespace Lunarstorm\LaravelDDD;

use Lunarstorm\LaravelDDD\Commands\InstallCommand;
use Lunarstorm\LaravelDDD\Commands\DomainActionMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainBaseModelMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainBaseViewModelMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainDtoMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainFactoryMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainModelMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainValueObjectMakeCommand;
use Lunarstorm\LaravelDDD\Commands\DomainViewModelMakeCommand;
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
                DomainModelMakeCommand::class,
                DomainFactoryMakeCommand::class,
                DomainBaseModelMakeCommand::class,
                DomainDtoMakeCommand::class,
                DomainValueObjectMakeCommand::class,
                DomainViewModelMakeCommand::class,
                DomainBaseViewModelMakeCommand::class,
                DomainActionMakeCommand::class,
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
