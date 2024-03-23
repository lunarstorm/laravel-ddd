<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

use Lunarstorm\LaravelDDD\Support\DomainResolver;

class DomainNamespaces
{
    public function __construct(
        public readonly string $root,
        public readonly string $models,
        public readonly string $factories,
        public readonly string $dataTransferObjects,
        public readonly string $viewModels,
        public readonly string $valueObjects,
        public readonly string $actions,
    ) {
    }

    public static function from(string $domain, ?string $subdomain = null): self
    {
        $domainWithSubdomain = str($domain)
            ->when($subdomain, fn ($domain) => $domain->append("\\{$subdomain}"))
            ->toString();

        $root = DomainResolver::getConfiguredDomainNamespace();

        $domainNamespace = implode('\\', [$root, $domainWithSubdomain]);

        return new self(
            root: $domainNamespace,
            models: "{$domainNamespace}\\".config('ddd.namespaces.models'),
            factories: "Database\\Factories\\{$domainWithSubdomain}",
            dataTransferObjects: "{$domainNamespace}\\".config('ddd.namespaces.data_transfer_objects'),
            viewModels: "{$domainNamespace}\\".config('ddd.namespaces.view_models'),
            valueObjects: "{$domainNamespace}\\".config('ddd.namespaces.value_objects'),
            actions: "{$domainNamespace}\\".config('ddd.namespaces.actions'),
        );
    }
}
