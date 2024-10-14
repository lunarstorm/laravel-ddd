<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Support\Str;

class DomainResolver
{
    /**
     * Get the list of current domain choices.
     */
    public static function domainChoices(): array
    {
        $folders = glob(app()->basePath(static::domainPath().'/*'), GLOB_ONLYDIR);

        return collect($folders)
            ->map(fn ($path) => basename($path))
            ->sort()
            ->toArray();
    }

    /**
     * Get the current configured domain path.
     */
    public static function domainPath(): ?string
    {
        return config('ddd.domain_path');
    }

    /**
     * Get the current configured root domain namespace.
     */
    public static function domainRootNamespace(): ?string
    {
        return config('ddd.domain_namespace');
    }

    public static function applicationLayerRootNamespace(): ?string
    {
        return config('ddd.application_layer.namespace', 'App\Modules');
    }

    /**
     * Resolve the relative domain object namespace.
     *
     * @param  string  $type  The domain object type.
     */
    public static function getRelativeObjectNamespace(string $type): string
    {
        return config("ddd.namespaces.{$type}", str($type)->plural()->studly()->toString());
    }

    public static function isApplicationLayer(string $type): bool
    {
        $applicationObjects = config('ddd.application_layer.objects', ['controller', 'request']);

        return in_array($type, $applicationObjects);
    }

    // public static function getDomainControllerNamespace(string $domain, ?string $controller = null): string
    // {
    //     $controllerRootNamespace = app()->getNamespace() . 'Http\Controllers';

    //     $namespace = collect([
    //         $controllerRootNamespace,
    //         $domain,
    //     ])->filter()->implode('\\');

    //     if ($controller) {
    //         $namespace .= "\\{$controller}";
    //     }

    //     return $namespace;
    // }

    public static function resolveRootNamespace(string $type): ?string
    {
        return static::isApplicationLayer($type)
            ? static::applicationLayerRootNamespace()
            : static::domainRootNamespace();
    }

    public static function getDomainObjectNamespace(string $domain, string $type, ?string $object = null): string
    {
        $namespace = collect([
            static::resolveRootNamespace($type),
            $domain,
            static::getRelativeObjectNamespace($type),
        ])->filter()->implode('\\');

        if ($object) {
            $namespace .= "\\{$object}";
        }

        return $namespace;
    }

    /**
     * Attempt to resolve the domain of a given domain class.
     */
    public static function guessDomainFromClass(string $class): ?string
    {
        if (! static::isDomainClass($class)) {
            // Not a domain object
            return null;
        }

        $domain = str($class)
            ->after(Str::finish(static::domainRootNamespace(), '\\'))
            ->before('\\')
            ->toString();

        return $domain;
    }

    /**
     * Attempt to resolve the file path of a given domain class.
     */
    public static function guessPathFromClass(string $class): ?string
    {
        if (! static::isDomainClass($class)) {
            // Not a domain object
            return null;
        }

        $classWithoutDomainRoot = str($class)
            ->after(Str::finish(static::domainRootNamespace(), '\\'))
            ->toString();

        return Path::join(...[static::domainPath(), "{$classWithoutDomainRoot}.php"]);
    }

    /**
     * Attempt to resolve the folder of a given domain class.
     */
    public static function guessFolderFromClass(string $class): ?string
    {
        $path = static::guessPathFromClass($class);

        if (! $path) {
            return null;
        }

        $filenamePortion = basename($path);

        return Str::beforeLast($path, $filenamePortion);
    }

    /**
     * Determine whether a class is an object within the domain layer.
     *
     * @param  string  $class  The fully qualified class name.
     */
    public static function isDomainClass(string $class): bool
    {
        return str($class)->startsWith(Str::finish(static::domainRootNamespace(), '\\'));
    }
}
