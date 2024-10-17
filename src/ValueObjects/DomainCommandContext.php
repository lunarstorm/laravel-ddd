<?php

namespace Lunarstorm\LaravelDDD\ValueObjects;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\Domain;

class DomainCommandContext
{
    public function __construct(
        public readonly string $command,
        public readonly ?string $domain,
        public readonly ?string $type,
        public readonly ?string $resource,
        public readonly array $arguments = [],
        public readonly array $options = [],

    ) {}

    public static function fromCommand(Command $command, ?Domain $domain = null, ?string $type = null): self
    {
        return new self(
            command: $command->getName(),
            domain: $domain?->domainWithSubdomain,
            type: $type,
            resource: $command->argument('name'),
            arguments: $command->arguments(),
            options: $command->options(),
        );
    }

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
