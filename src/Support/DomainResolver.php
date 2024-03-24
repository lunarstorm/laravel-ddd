<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Support\Str;

class DomainResolver
{
    public static function domainChoices(): array
    {
        $folders = glob(app()->basePath(static::domainPath().'/*'), GLOB_ONLYDIR);

        return collect($folders)
            ->map(function ($folder) {
                return basename($folder);
            })
            ->sort()
            ->toArray();
    }

    public static function domainPath(): ?string
    {
        return config('ddd.domain_path');
    }

    public static function domainRootNamespace(): ?string
    {
        return config('ddd.domain_namespace');
    }

    public static function getRelativeObjectNamespace(string $type): string
    {
        return config("ddd.namespaces.{$type}", str($type)->plural()->studly()->toString());
    }

    public static function getDomainObjectNamespace(string $domain, string $type): string
    {
        return implode('\\', [static::domainRootNamespace(), $domain, static::getRelativeObjectNamespace($type)]);
    }

    public static function guessDomainFromClass(string $class): ?string
    {
        $domainNamespace = Str::finish(DomainResolver::domainRootNamespace(), '\\');

        if (! str($class)->startsWith($domainNamespace)) {
            // Not a domain object
            return null;
        }

        $domain = str($class)
            ->after($domainNamespace)
            ->before('\\')
            ->toString();

        return $domain;
    }
}
