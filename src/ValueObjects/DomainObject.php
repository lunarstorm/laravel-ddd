<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\Path;

class DomainObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $domain,
        public readonly string $namespace,
        public readonly string $fqn,
        public readonly string $path,
        public readonly ?string $type = null,
    ) {
    }

    public static function fromClass(string $fullyQualifiedClass, ?string $objectType = null): ?self
    {
        if (! DomainResolver::isDomainClass($fullyQualifiedClass)) {
            return null;
        }

        // First extract the object base name
        $objectName = class_basename($fullyQualifiedClass);

        $objectNamespace = '';

        $possibleObjectNamespaces = config("ddd.namespaces.{$objectType}")
            ? [$objectType => config("ddd.namespaces.{$objectType}")]
            : config('ddd.namespaces', []);

        foreach ($possibleObjectNamespaces as $type => $namespace) {
            $rootObjectNamespace = preg_quote($namespace);

            $pattern = "/({$rootObjectNamespace})(.*)$/";

            $result = preg_match($pattern, $fullyQualifiedClass, $matches);

            if (! $result) {
                continue;
            }

            $objectNamespace = str(data_get($matches, 0))->beforeLast('\\')->toString();

            $objectType = $type;

            break;
        }

        // If there wasn't a recognized namespace, we'll assume it's a
        // domain object in an ad-hoc namespace.
        if (! $objectNamespace) {
            // e.g., Domain\Invoicing\AdHoc\Nested\Thing
            $objectNamespace = str($fullyQualifiedClass)
                ->after(Str::finish(DomainResolver::domainRootNamespace(), '\\'))
                ->after('\\')
                ->before("\\{$objectName}")
                ->toString();
        }

        // Extract the domain portion
        $domainName = str($fullyQualifiedClass)
            ->after(Str::finish(DomainResolver::domainRootNamespace(), '\\'))
            ->before("\\{$objectNamespace}")
            ->toString();

        // Reconstruct the path
        $path = Path::join(
            DomainResolver::domainPath(),
            $domainName,
            $objectNamespace,
            "{$objectName}.php",
        );

        return new self(
            name: $objectName,
            domain: $domainName,
            namespace: $objectNamespace,
            fqn: $fullyQualifiedClass,
            path: $path,
            type: $objectType,
        );
    }
}
