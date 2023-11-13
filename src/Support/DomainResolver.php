<?php

namespace Lunarstorm\LaravelDDD\Support;

class DomainResolver
{
    public static function guessDomainFromClass(string $class): ?string
    {
        $domainNamespace = basename(config('ddd.paths.domains')).'\\';

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
