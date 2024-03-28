<?php

namespace Lunarstorm\LaravelDDD;

use Illuminate\Support\Facades\Event;
use Lunarstorm\LaravelDDD\Listeners\CacheClearSubscriber;
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
                Commands\InstallCommand::class,
                Commands\DomainListCommand::class,
                Commands\DomainModelMakeCommand::class,
                Commands\DomainFactoryMakeCommand::class,
                Commands\DomainBaseModelMakeCommand::class,
                Commands\DomainDtoMakeCommand::class,
                Commands\DomainValueObjectMakeCommand::class,
                Commands\DomainViewModelMakeCommand::class,
                Commands\DomainBaseViewModelMakeCommand::class,
                Commands\DomainActionMakeCommand::class,
                Commands\DomainCastMakeCommand::class,
                Commands\DomainChannelMakeCommand::class,
                Commands\DomainConsoleMakeCommand::class,
                Commands\DomainEventMakeCommand::class,
                Commands\DomainExceptionMakeCommand::class,
                Commands\DomainJobMakeCommand::class,
                Commands\DomainListenerMakeCommand::class,
                Commands\DomainMailMakeCommand::class,
                Commands\DomainNotificationMakeCommand::class,
                Commands\DomainObserverMakeCommand::class,
                Commands\DomainPolicyMakeCommand::class,
                Commands\DomainProviderMakeCommand::class,
                Commands\DomainResourceMakeCommand::class,
                Commands\DomainRuleMakeCommand::class,
                Commands\DomainScopeMakeCommand::class,
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

    public function packageRegistered()
    {
        (new DomainAutoloader())->autoload();
        Event::subscribe(CacheClearSubscriber::class);
    }
}
