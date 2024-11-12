<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Symfony\Component\Process\Process;

trait UpdatesComposer
{
    public function fillComposerValue($path, $value)
    {
        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);
        data_fill($data, $path, $value);

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $this;
    }

    public function registerPsr4Autoload($namespace, $path)
    {
        $namespace = str($namespace)
            ->rtrim('/\\')
            ->finish('\\')
            ->toString();

        $this->comment("Registering `{$namespace}`:`{$path}` in composer.json...");

        $this->fillComposerValue(['autoload', 'psr-4', $namespace], $path);

        $this->composerReload();

        return $this;
    }

    protected function composerReload()
    {
        $composer = $this->hasOption('composer') ? $this->option('composer') : 'global';

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

        return $this;
    }
}
