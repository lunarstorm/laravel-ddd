<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Support\Str;

class DomainResolver
{
    public static function getConfiguredDomainPath(): ?string
    {
        return config('ddd.domain_path');
    }

    public static function getConfiguredDomainNamespace(): ?string
    {
        return config('ddd.domain_namespace');
    }

    public static function getRelativeObjectNamespace(string $type): string
    {
        return config("ddd.namespaces.{$type}", str($type)->plural()->studly()->toString());
    }

    public static function getDomainObjectNamespace(string $domain, string $type): string
    {
        return implode('\\', [static::getConfiguredDomainNamespace(), $domain, static::getRelativeObjectNamespace($type)]);
    }

    public static function guessDomainFromClass(string $class): ?string
    {
        $domainNamespace = Str::finish(DomainResolver::getConfiguredDomainNamespace(), '\\');

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
