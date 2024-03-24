<?php

namespace Lunarstorm\LaravelDDD\Support;

use Lunarstorm\LaravelDDD\ValueObjects\DomainNamespaces;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;

class Domain
{
    public readonly string $dotName;

    public readonly string $path;

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
    }

    protected function registerDomainObjects()
    {
    }

    protected function registerDomainObject()
    {
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

    public function relativePath(string $path = ''): string
    {
        return collect([$this->domain, $path])->filter()->implode(DIRECTORY_SEPARATOR);
    }

    public function namespaceFor(string $type): string
    {
        return DomainResolver::getDomainObjectNamespace($this->domainWithSubdomain, $type);
    }

    public function object(string $type, string $name): DomainObject
    {
        $namespace = $this->namespaceFor($type);

        $name = str($name)->replace("{$namespace}\\", '')->toString();

        return new DomainObject(
            name: $name,
            namespace: $namespace,
            fqn: $namespace . '\\' . $name,
            path: $this->path($namespace . '\\' . $name),
        );
    }

    public function model(string $name): DomainObject
    {
        return $this->object('model', $name);
    }

    public function factory(string $name): DomainObject
    {
        $name = str($name)->replace($this->namespace->root, '')->toString();

        return new DomainObject(
            name: $name,
            namespace: $this->namespace->factories,
            fqn: $this->namespace->factories . '\\' . $name,
            path: str("database/factories/{$this->domainWithSubdomain}/{$name}.php")
                ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
                ->toString()
        );
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
