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
        public readonly string $casts,
        public readonly string $commands,
        public readonly string $enums,
        public readonly string $events,
        public readonly string $exceptions,
        public readonly string $jobs,
        public readonly string $mail,
        public readonly string $notifications,
        public readonly string $resources,
        public readonly string $rules,
    ) {}

    public static function from(string $domain, ?string $subdomain = null): self
    {
        $domainWithSubdomain = str($domain)
            ->when($subdomain, fn ($domain) => $domain->append("\\{$subdomain}"))
            ->toString();

        $root = DomainResolver::domainRootNamespace();

        $domainNamespace = implode('\\', [$root, $domainWithSubdomain]);

        return new self(
            root: $domainNamespace,
            models: "{$domainNamespace}\\".config('ddd.namespaces.model', 'Models'),
            factories: "Database\\Factories\\{$domainWithSubdomain}",
            dataTransferObjects: "{$domainNamespace}\\".config('ddd.namespaces.data_transfer_object', 'Data'),
            viewModels: "{$domainNamespace}\\".config('ddd.namespaces.view_model', 'ViewModels'),
            valueObjects: "{$domainNamespace}\\".config('ddd.namespaces.value_object', 'ValueObjects'),
            actions: "{$domainNamespace}\\".config('ddd.namespaces.action', 'Actions'),
            enums: "{$domainNamespace}\\".config('ddd.namespaces.enums', 'Enums'),
            events: "{$domainNamespace}\\".config('ddd.namespaces.event', 'Events'),
            casts: "{$domainNamespace}\\".config('ddd.namespaces.cast', 'Casts'),
            commands: "{$domainNamespace}\\".config('ddd.namespaces.command', 'Commands'),
            exceptions: "{$domainNamespace}\\".config('ddd.namespaces.exception', 'Exceptions'),
            jobs: "{$domainNamespace}\\".config('ddd.namespaces.job', 'Jobs'),
            mail: "{$domainNamespace}\\".config('ddd.namespaces.mail', 'Mail'),
            notifications: "{$domainNamespace}\\".config('ddd.namespaces.notification', 'Notifications'),
            resources: "{$domainNamespace}\\".config('ddd.namespaces.resource', 'Resources'),
            rules: "{$domainNamespace}\\".config('ddd.namespaces.rule', 'Rules'),
        );
    }
}
