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

    public static function guessDomainFromClass(string $class): ?string
    {
        $domainNamespace = Str::finish(DomainResolver::getConfiguredDomainNamespace(), '\\');

        if (! str($class)->startsWith($domainNamespace)) {
            // Not a domain model
            return null;
        }

        $domain = str($class)
            ->after($domainNamespace)
            ->before('\\')
            ->toString();

        return $domain;
    }
}
