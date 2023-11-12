<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Support\Str;

class DomainResolver
{
    public static function fromClass(string $class): ?Domain
    {
        $domainNamespace = basename(config('ddd.paths.domains')).'\\';

        if (! Str::startsWith($class, $domainNamespace)) {
            // Not a domain model
            return null;
        }

        $domain = str($class)
            ->after($domainNamespace)
            ->before('\\')
            ->toString();

        return new Domain($domain);
    }
}
