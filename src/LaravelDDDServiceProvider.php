<?php

namespace Lunarstorm\LaravelDDD;

use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Foundation\Application;
use Lunarstorm\LaravelDDD\Facades\Autoload;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainMigration;
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

        if ($this->app->runningUnitTests()) {
            $package->hasRoutes(['testing']);
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

        $this->app->when(MigrationCreator::class)
            ->needs('$customStubPath')
            ->give(function ($app) {
                return $app->basePath('stubs');
            });

        $this->loadMigrationsFrom(DomainMigration::paths());

        return $this;
    }

    protected function registerBindings()
    {
        $this->app->scoped(DomainManager::class, function () {
            return new DomainManager;
        });

        $this->app->scoped(ComposerManager::class, function () {
            return ComposerManager::make(app()->basePath('composer.json'));
        });

        $this->app->scoped(ConfigManager::class, function () {
            return new ConfigManager(config_path('ddd.php'));
        });

        $this->app->scoped(StubManager::class, function () {
            return new StubManager;
        });

        $this->app->scoped(AutoloadManager::class, function () {
            return new AutoloadManager;
        });

        $this->app->bind('ddd', DomainManager::class);
        $this->app->bind('ddd.autoloader', AutoloadManager::class);
        $this->app->bind('ddd.config', ConfigManager::class);
        $this->app->bind('ddd.composer', ComposerManager::class);
        $this->app->bind('ddd.stubs', StubManager::class);

        if ($this->app->runningUnitTests()) {
            // $this->app->when(AutoloadManager::class)
            //     ->needs(Application::class)
            //     ->give(function () {
            //         return $this->app;
            //     });

            $this->app->resolving(AutoloadManager::class, function (AutoloadManager $atuoloader, Application $app) {
                // dump('App resolving autoloader');
            });
        }

        return $this;
    }

    public function packageBooted()
    {
        Autoload::run();

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
        $this->registerMigrations()
            ->registerBindings();
    }
}
