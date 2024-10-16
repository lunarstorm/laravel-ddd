<?php

namespace Lunarstorm\LaravelDDD\Support;

class Layer
{
    public readonly ?string $namespace;

    public readonly ?string $path;

    public function __construct(
        ?string $namespace,
        ?string $path,
    ) {
        $this->namespace = Path::normalizeNamespace($namespace);
        $this->path = is_null($path)
            ? $this->path()
            : Path::normalize($path);
    }

    public static function fromNamespace(string $namespace): self
    {
        return new self($namespace, null);
    }

    public function path(?string $path = null): string
    {
        if (is_null($path)) {
            return $this->path;
        }

        $relativePath = str($path)
            ->replace($this->namespace, '')
            ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
            ->append('.php')
            ->toString();

        return Path::join($this->path, $relativePath);
    }

    public function namespaceFor(string $type, ?string $name = null): string
    {
        $namespace = collect([
            $this->namespace,
            DomainResolver::getRelativeObjectNamespace($type),
        ])->filter()->implode('\\');

        if ($name) {
            $namespace .= "\\{$name}";
        }

        return Path::normalizeNamespace($namespace);
    }

    public function guessNamespaceFromName(string $name): string
    {
        $baseName = class_basename($name);

        return Path::normalizeNamespace(
            str($name)
                ->before($baseName)
                ->trim('\\')
                ->prepend($this->namespace.'\\')
                ->toString()
        );
    }
}
