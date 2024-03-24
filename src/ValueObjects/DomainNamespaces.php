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
            models: "{$domainNamespace}\\".config('ddd.namespaces.models', 'Models'),
            factories: "Database\\Factories\\{$domainWithSubdomain}",
            dataTransferObjects: "{$domainNamespace}\\".config('ddd.namespaces.data_transfer_objects', 'Data'),
            viewModels: "{$domainNamespace}\\".config('ddd.namespaces.view_models', 'ViewModels'),
            valueObjects: "{$domainNamespace}\\".config('ddd.namespaces.value_objects', 'ValueObjects'),
            actions: "{$domainNamespace}\\".config('ddd.namespaces.actions', 'Actions'),
            enums: "{$domainNamespace}\\".config('ddd.namespaces.enums', 'Enums'),
            events: "{$domainNamespace}\\".config('ddd.namespaces.events', 'Events'),
            casts: "{$domainNamespace}\\".config('ddd.namespaces.casts', 'Casts'),
            commands: "{$domainNamespace}\\".config('ddd.namespaces.commands', 'Commands'),
            exceptions: "{$domainNamespace}\\".config('ddd.namespaces.exceptions', 'Exceptions'),
            jobs: "{$domainNamespace}\\".config('ddd.namespaces.jobs', 'Jobs'),
            mail: "{$domainNamespace}\\".config('ddd.namespaces.mail', 'Mail'),
            notifications: "{$domainNamespace}\\".config('ddd.namespaces.notifications', 'Notifications'),
            resources: "{$domainNamespace}\\".config('ddd.namespaces.resources', 'Resources'),
            rules: "{$domainNamespace}\\".config('ddd.namespaces.rules', 'Rules'),
        );
    }
}
