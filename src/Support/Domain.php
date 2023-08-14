<?php

namespace Lunarstorm\LaravelDDD\Support;

class Domain
{
    public string $domainRoot;

    public function __construct(public string $domain)
    {
        $this->domainRoot = basename(config('ddd.paths.domains'));
    }

    public function relativePath(string $path = ''): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->domain,
            $path,
        ]);
    }

    public function namespacedModel(string $model): string
    {
        $prefix = implode('\\', [
            $this->domainRoot,
            $this->domain,
            config('ddd.namespaces.models'),
        ]);

        $model = str($model)
            ->replace($prefix, '')
            ->ltrim('\\')
            ->toString();

        return implode('\\', [$prefix, $model]);
    }
}
