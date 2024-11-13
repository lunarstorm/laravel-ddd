<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\InteractsWithComposerPackages;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;

class InstallCommand extends Command
{
    use InteractsWithComposerPackages;

    public $signature = 'ddd:install {--composer=global : Absolute path to the Composer binary which should be used}';

    protected $description = 'Install and initialize Laravel-DDD';

    public function handle(): int
    {
        $this->call('ddd:publish', ['--config' => true]);

        $this->comment('Updating composer.json...');
        $this->callSilently('ddd:config', ['action' => 'composer']);

        if (confirm('Would you like to publish stubs now?', default: false, hint: 'You may do this at any time via ddd:stub')) {
            $this->call('ddd:stub');
        }

        return self::SUCCESS;
    }

    // public function registerDomainAutoload()
    // {
    //     $domainPath = DomainResolver::domainPath();

    //     $domainRootNamespace = str(DomainResolver::domainRootNamespace())
    //         ->rtrim('/\\')
    //         ->toString();

    //     $this->comment("Registering domain path `{$domainPath}` in composer.json...");

    //     $composerFile = base_path('composer.json');
    //     $data = json_decode(file_get_contents($composerFile), true);
    //     data_fill($data, ['autoload', 'psr-4', $domainRootNamespace . '\\'], $domainPath);

    //     file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    //     $this->composerReload();
    // }

    // protected function composerReload()
    // {
    //     $composer = $this->option('composer');

    //     if ($composer !== 'global') {
    //         $command = ['php', $composer, 'dump-autoload'];
    //     } else {
    //         $command = ['composer', 'dump-autoload'];
    //     }

    //     (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
    //         ->setTimeout(null)
    //         ->run(function ($type, $output) {
    //             $this->output->write($output);
    //         });
    // }
}
