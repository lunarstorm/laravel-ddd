<?php

namespace Lunarstorm\LaravelDDD\Support\Concerns;

trait InteractsWithComposer
{
    public function composerFill($path, $value)
    {
        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);

        data_fill($data, $path, $value);

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $this;
    }

    public function composerRegisterAutoload($namespace, $path)
    {
        $namespace = str($namespace)
            ->rtrim('/\\')
            ->finish('\\')
            ->toString();

        $this->composerFill(['autoload', 'psr-4', $namespace], $path);

        return $this;
    }
}
