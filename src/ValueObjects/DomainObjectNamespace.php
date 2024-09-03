<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

class DomainObjectNamespace
{
    public function __construct(
        public readonly string $type,
        public readonly string $namespace,
    ) {}

    public static function make(string $key, string $domain, ?string $subdomain = null): self
    {
        $domainWithSubdomain = str($domain)
            ->when($subdomain, fn ($domain) => $domain->append("\\{$subdomain}"))
            ->toString();

        $root = DomainResolver::domainRootNamespace();

        $domainNamespace = implode('\\', [$root, $domainWithSubdomain]);

        $namespace = "{$domainNamespace}\\".config("ddd.namespaces.{$key}", Str::studly($key));

        return new self(type: $key, namespace: $namespace);
    }
}
