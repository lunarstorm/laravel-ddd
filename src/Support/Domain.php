<?php

namespace Lunarstorm\LaravelDDD\Support;

use Lunarstorm\LaravelDDD\ValueObjects\DomainNamespaces;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;

class Domain
{
    public readonly string $dotName;

    public readonly string $path;

    public readonly string $migrationPath;

    public readonly string $domain;

    public readonly ?string $subdomain;

    public readonly string $domainWithSubdomain;

    public readonly DomainNamespaces $namespace;

    public static array $objects = [];

    public function __construct(string $domain, ?string $subdomain = null)
    {
        if (is_null($subdomain)) {
            // If a subdomain isn't explicitly specified, we
            // will attempt to parse it from the domain.
            $parts = str($domain)
                ->replace(['\\', '/'], '.')
                ->explode('.')
                ->filter();

            $domain = $parts->shift();

            if ($parts->count() > 0) {
                $subdomain = $parts->implode('.');
            }
        }

        $domain = str($domain)->trim('\\/')->toString();

        $subdomain = str($subdomain)->trim('\\/')->toString();

        $this->domainWithSubdomain = str($domain)
            ->when($subdomain, fn ($domain) => $domain->append("\\{$subdomain}"))
            ->toString();

        $this->domain = $domain;

        $this->subdomain = $subdomain ?: null;

        $this->dotName = $this->subdomain
            ? "{$this->domain}.{$this->subdomain}"
            : $this->domain;

        $this->namespace = DomainNamespaces::from($this->domain, $this->subdomain);

        $this->path = Path::join(DomainResolver::domainPath(), $this->domainWithSubdomain);

        $this->migrationPath = Path::join($this->path, config('ddd.namespaces.migration', 'Database/Migrations'));
    }

    protected function getDomainBasePath()
    {
        return app()->basePath(DomainResolver::domainPath());
    }

    public function path(?string $path = null): string
    {
        if (is_null($path)) {
            return $this->path;
        }

        $path = str($path)
            ->replace($this->namespace->root, '')
            ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
            ->append('.php')
            ->toString();

        return Path::join($this->path, $path);
    }

    public function pathInApplicationLayer(?string $path = null): string
    {
        if (is_null($path)) {
            return $this->path;
        }

        $path = str($path)
            ->replace(app()->getNamespace(), '')
            ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
            ->append('.php')
            ->toString();

        return Path::join('app', $path);
    }

    public function relativePath(string $path = ''): string
    {
        return collect([$this->domain, $path])->filter()->implode(DIRECTORY_SEPARATOR);
    }

    public function namespaceFor(string $type): string
    {
        return DomainResolver::getDomainObjectNamespace($this->domainWithSubdomain, $type);
    }

    public function guessNamespaceFromName(string $name): string
    {
        $baseName = class_basename($name);

        return str($name)
            ->before($baseName)
            ->trim('\\')
            ->prepend(DomainResolver::domainRootNamespace().'\\'.$this->domainWithSubdomain.'\\')
            ->toString();
    }

    public function object(string $type, string $name, bool $absolute = false): DomainObject
    {
        $namespace = match (true) {
            $absolute => $this->namespace->root,
            str($name)->startsWith('\\') => $this->guessNamespaceFromName($name),
            default => $this->namespaceFor($type),
        };

        $baseName = str($name)->replace($namespace, '')
            ->replace(['\\', '/'], '\\')
            ->trim('\\')
            ->toString();

        return new DomainObject(
            name: $baseName,
            domain: $this->domain,
            namespace: $namespace,
            fullyQualifiedName: $namespace.'\\'.$baseName,
            path: DomainResolver::isApplicationLayer($type)
                ? $this->pathInApplicationLayer($namespace.'\\'.$baseName)
                : $this->path($namespace.'\\'.$baseName),
            type: $type
        );
    }

    public function model(string $name): DomainObject
    {
        return $this->object('model', $name);
    }

    public function factory(string $name): DomainObject
    {
        return $this->object('factory', $name);
    }

    public function dataTransferObject(string $name): DomainObject
    {
        return $this->object('data_transfer_object', $name);
    }

    public function dto(string $name): DomainObject
    {
        return $this->dataTransferObject($name);
    }

    public function viewModel(string $name): DomainObject
    {
        return $this->object('view_model', $name);
    }

    public function valueObject(string $name): DomainObject
    {
        return $this->object('value_object', $name);
    }

    public function action(string $name): DomainObject
    {
        return $this->object('action', $name);
    }

    public function cast(string $name): DomainObject
    {
        return $this->object('cast', $name);
    }

    public function command(string $name): DomainObject
    {
        return $this->object('command', $name);
    }

    public function enum(string $name): DomainObject
    {
        return $this->object('enum', $name);
    }

    public function job(string $name): DomainObject
    {
        return $this->object('job', $name);
    }

    public function mail(string $name): DomainObject
    {
        return $this->object('mail', $name);
    }

    public function notification(string $name): DomainObject
    {
        return $this->object('notification', $name);
    }

    public function resource(string $name): DomainObject
    {
        return $this->object('resource', $name);
    }

    public function rule(string $name): DomainObject
    {
        return $this->object('rule', $name);
    }

    public function event(string $name): DomainObject
    {
        return $this->object('event', $name);
    }

    public function exception(string $name): DomainObject
    {
        return $this->object('exception', $name);
    }
}
