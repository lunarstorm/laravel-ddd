<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

class DomainObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $namespace,
        public readonly string $fqn,
        public readonly string $path,
    ) {
    }
}
