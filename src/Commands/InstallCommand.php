<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    public $signature = 'ddd:install {--composer=global : Absolute path to the Composer binary which should be used}';

    public $description = 'Initializes the configured Domain path in composer.json.';

    public function handle(): int
    {
        $this->initializeComposerAutoload();
        $this->composerReload();

        return self::SUCCESS;
    }

    public function initializeComposerAutoload()
    {
        $domainPath = config('ddd.paths.domains');

        $this->comment("Registering domain path `{$domainPath}` in composer.json...");

        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);
        data_fill($data, ['autoload', 'psr-4', 'Domains\\'], $domainPath);

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
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
