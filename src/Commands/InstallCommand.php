<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    public $signature = 'ddd:install {--composer=global : Absolute path to the Composer binary which should be used}';

    protected $description = 'Install and initialize Laravel-DDD';

    public function handle(): int
    {
        $this->comment('Publishing config...');
        $this->call('vendor:publish', [
            '--tag' => 'ddd-config',
        ]);

        $this->comment('Ensuring domain path is registered in composer.json...');
        $this->registerDomainAutoload();

        if ($this->confirm('Would you like to publish stubs?')) {
            $this->comment('Publishing stubs...');

            $this->callSilently('vendor:publish', [
                '--tag' => 'ddd-stubs',
            ]);
        }

        return self::SUCCESS;
    }

    public function registerDomainAutoload()
    {
        $domainPath = DomainResolver::domainPath();

        $domainRootNamespace = str(DomainResolver::domainRootNamespace())
            ->rtrim('/\\')
            ->toString();

        $this->comment("Registering domain path `{$domainPath}` in composer.json...");

        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);
        data_fill($data, ['autoload', 'psr-4', $domainRootNamespace . '\\'], $domainPath);

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->composerReload();
    }

    protected function composerReload()
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'dump-autoload'];
        } else {
            $command = ['composer', 'dump-autoload'];
        }

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }
}
