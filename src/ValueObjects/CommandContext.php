<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

class CommandContext
{
    public function __construct(
        public readonly string $name,
        public readonly array $arguments = [],
        public readonly array $options = []
    ) {}

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function option(string $key): mixed
    {
        return data_get($this->options, $key);
    }

    public function hasArgument(string $key): bool
    {
        return array_key_exists($key, $this->arguments);
    }

    public function argument(string $key): mixed
    {
        return data_get($this->arguments, $key);
    }
}
