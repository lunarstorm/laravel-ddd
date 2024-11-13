<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Lunarstorm\LaravelDDD\ValueObjects\CommandContext;
use Lunarstorm\LaravelDDD\ValueObjects\ObjectSchema;

class GeneratorBlueprint
{
    public string $nameInput;

    public string $domainName;

    public ?Domain $domain = null;

    public CommandContext $command;

    public ObjectSchema $schema;

    public Layer $layer;

    public bool $isAbsoluteName;

    public string $type;

    public function __construct(
        string $nameInput,
        string $domainName,
        Command $command,
    ) {
        $this->nameInput = str($nameInput)->studly()->replace(['.', '\\', '/'], '/')->toString();

        $this->domain = new Domain($domainName);

        $this->domainName = $this->domain->domainWithSubdomain;

        $this->command = new CommandContext($command->getName(), $command->arguments(), $command->options());

        $this->isAbsoluteName = str($this->nameInput)->startsWith('/');

        $this->type = $this->guessObjectType();

        $this->layer = DomainResolver::resolveLayer($this->domainName, $this->type);

        $this->schema = $this->resolveSchema();
    }

    protected function guessObjectType(): string
    {
        return match ($this->command->name) {
            'ddd:base-view-model' => 'view_model',
            'ddd:base-model' => 'model',
            'ddd:value' => 'value_object',
            'ddd:dto' => 'data_transfer_object',
            'ddd:migration' => 'migration',
            default => str($this->command->name)->after(':')->snake()->toString(),
        };
    }

    protected function resolveSchema(): ObjectSchema
    {
        $customResolver = app('ddd')->getObjectSchemaResolver();

        $blueprint = is_callable($customResolver)
            ? App::call($customResolver, [
                'domainName' => $this->domainName,
                'nameInput' => $this->nameInput,
                'type' => $this->type,
                'command' => $this->command,
            ])
            : null;

        if ($blueprint instanceof ObjectSchema) {
            return $blueprint;
        }

        $namespace = match (true) {
            $this->isAbsoluteName => $this->layer->namespace,
            str($this->nameInput)->startsWith('\\') => $this->layer->guessNamespaceFromName($this->nameInput),
            default => $this->layer->namespaceFor($this->type),
        };

        $baseName = str($this->nameInput)->replace($namespace, '')
            ->replace(['\\', '/'], '\\')
            ->trim('\\')
            ->when($this->type === 'factory', fn ($name) => $name->finish('Factory'))
            ->toString();

        $fullyQualifiedName = $namespace.'\\'.$baseName;

        return new ObjectSchema(
            name: $this->nameInput,
            namespace: $namespace,
            fullyQualifiedName: $fullyQualifiedName,
            path: $this->layer->path($fullyQualifiedName),
        );
    }

    public function rootNamespace()
    {
        return str($this->schema->namespace)->finish('\\')->toString();
    }

    public function getDefaultNamespace($rootNamespace)
    {
        return $this->schema->namespace;
    }

    public function getPath($name)
    {
        return Path::normalize(app()->basePath($this->schema->path));
    }

    public function qualifyClass($name)
    {
        return $this->schema->fullyQualifiedName;
    }

    public function getFactoryFor(string $name)
    {
        return $this->domain->factory($name);
    }

    public function getMigrationPath()
    {
        return $this->domain->migrationPath;
    }

    public function getNamespaceFor($type, $name = null)
    {
        return $this->domain->namespaceFor($type, $name);
    }
}
