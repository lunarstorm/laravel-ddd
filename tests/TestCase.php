<?php

namespace Lunarstorm\LaravelDDD\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\LaravelDDDServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Process\Process;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);

        // Reset the domain namespace
        Arr::forget($data, ['autoload', 'psr-4', 'Domains\\']);

        // Allow pest-plugin
        data_fill($data, ['config', 'allow-plugins'], true);

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->composerReload();

        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Lunarstorm\\LaravelDDD\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->beforeApplicationDestroyed(function () {
            File::cleanDirectory(app_path());

            File::deleteDirectories([
                base_path('Custom'),
                base_path('src/Domains'),
            ]);
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelDDDServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    protected function composerReload()
    {
        $command = ['composer', 'dump-autoload'];

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
            });
    }
}
