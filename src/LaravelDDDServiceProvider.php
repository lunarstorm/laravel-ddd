<?php

namespace Lunarstorm\LaravelDDD;

use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainMigration;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDDDServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $this->app->scoped(DomainManager::class, function () {
            return new DomainManager;
        });

        $this->app->scoped(ConfigManager::class, function () {
            return new ConfigManager(config_path('ddd.php'));
        });

        $this->app->scoped(ComposerManager::class, function () {
            return ComposerManager::make(app()->basePath('composer.json'));
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
                Commands\ConfigCommand::class,
                Commands\PublishCommand::class,
                Commands\StubCommand::class,
                Commands\UpgradeCommand::class,
                Commands\OptimizeCommand::class,
                Commands\OptimizeClearCommand::class,
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
                Commands\DomainMiddlewareMakeCommand::class,
                Commands\DomainNotificationMakeCommand::class,
                Commands\DomainObserverMakeCommand::class,
                Commands\DomainPolicyMakeCommand::class,
                Commands\DomainProviderMakeCommand::class,
                Commands\DomainResourceMakeCommand::class,
                Commands\DomainRequestMakeCommand::class,
                Commands\DomainRuleMakeCommand::class,
                Commands\DomainScopeMakeCommand::class,
                Commands\DomainSeederMakeCommand::class,
                Commands\Migration\DomainMigrateMakeCommand::class,
            ]);

        if ($this->laravelVersion(11)) {
            $package->hasCommand(Commands\DomainClassMakeCommand::class);
            $package->hasCommand(Commands\DomainEnumMakeCommand::class);
            $package->hasCommand(Commands\DomainInterfaceMakeCommand::class);
            $package->hasCommand(Commands\DomainTraitMakeCommand::class);
        }
    }

    protected function laravelVersion($value)
    {
        return version_compare(app()->version(), $value, '>=');
    }

    protected function registerMigrations()
    {
        $this->app->singleton(Commands\Migration\DomainMigrateMakeCommand::class, function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];
            $composer = $app['composer'];

            return new Commands\Migration\DomainMigrateMakeCommand($creator, $composer);
        });

        $this->loadMigrationsFrom(DomainMigration::paths());
    }

    public function packageBooted()
    {
        // $this->publishes([
        //     $this->package->basePath('/../stubs') => $this->app->basePath("stubs/{$this->package->shortName()}"),
        // ], "{$this->package->shortName()}-stubs");

        if ($this->app->runningInConsole() && method_exists($this, 'optimizes')) {
            $this->optimizes(
                optimize: 'ddd:optimize',
                clear: 'ddd:clear',
                key: 'laravel-ddd',
            );
        }
    }

    public function packageRegistered()
    {
        (new DomainAutoloader)->autoload();

        $this->registerMigrations();
    }
}
