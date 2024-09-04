<?php

namespace Lunarstorm\LaravelDDD;

use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDDDServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $this->app->scoped(DomainManager::class, function () {
            return new DomainManager;
        });

        $this->app->bind('ddd', DomainManager::class);

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
                Commands\UpgradeCommand::class,
                Commands\CacheCommand::class,
                Commands\CacheClearCommand::class,
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
                Commands\DomainControllerMakeCommand::class,
                Commands\DomainEventMakeCommand::class,
                Commands\DomainExceptionMakeCommand::class,
                Commands\DomainJobMakeCommand::class,
                Commands\DomainListenerMakeCommand::class,
                Commands\DomainMailMakeCommand::class,
                // Commands\DomainMigrateMakeCommand::class,
                Commands\DomainNotificationMakeCommand::class,
                Commands\DomainObserverMakeCommand::class,
                Commands\DomainPolicyMakeCommand::class,
                Commands\DomainProviderMakeCommand::class,
                Commands\DomainResourceMakeCommand::class,
                Commands\DomainRuleMakeCommand::class,
                Commands\DomainScopeMakeCommand::class,
                Commands\DomainSeederMakeCommand::class,
            ]);

        if (app()->version() >= 11) {
            $package->hasCommand(Commands\DomainClassMakeCommand::class);
            $package->hasCommand(Commands\DomainEnumMakeCommand::class);
            $package->hasCommand(Commands\DomainInterfaceMakeCommand::class);
            $package->hasCommand(Commands\DomainTraitMakeCommand::class);
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
        (new DomainAutoloader)->autoload();
    }
}
