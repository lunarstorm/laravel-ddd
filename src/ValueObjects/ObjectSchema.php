<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

class ObjectSchema
{
    public function __construct(
        public readonly string $name,
        public readonly string $namespace,
        public readonly string $fullyQualifiedName,
        public readonly string $path,
    ) {}
}
