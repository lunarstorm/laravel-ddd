<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\Path;
use Symfony\Component\Console\Input\InputOption;

trait ResolvesDomainFromInput
{
    use CanPromptForDomain,
        HandleHooks,
        QualifiesDomainModels;

    protected $nameIsAbsolute = false;

    protected ?Domain $domain = null;

    protected function getOptions()
    {
        return [
            ...parent::getOptions(),
            ['domain', null, InputOption::VALUE_OPTIONAL, 'The domain name'],
        ];
    }

    protected function rootNamespace()
    {
        $type = $this->guessObjectType();

        return Str::finish(DomainResolver::resolveRootNamespace($type), '\\');
    }

    protected function guessObjectType(): string
    {
        return match ($this->name) {
            'ddd:base-view-model' => 'view_model',
            'ddd:base-model' => 'model',
            'ddd:value' => 'value_object',
            'ddd:dto' => 'data_transfer_object',
            'ddd:migration' => 'migration',
            default => str($this->name)->after(':')->snake()->toString(),
        };
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->domain) {
            return $this->nameIsAbsolute
                ? $this->domain->namespace->root
                : $this->domain->namespaceFor($this->guessObjectType());
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function getPath($name)
    {
        if ($this->domain) {
            return Path::normalize($this->laravel->basePath(
                $this->domain->object(
                    type: $this->guessObjectType(),
                    name: $name,
                    absolute: $this->nameIsAbsolute
                )->path
            ));
        }

        return parent::getPath($name);
    }

    protected function beforeHandle()
    {
        $nameInput = $this->getNameInput();

        // If the name contains a domain prefix, extract it
        // and strip it from the name argument.
        $domainExtractedFromName = null;

        if (Str::contains($nameInput, ':')) {
            $domainExtractedFromName = Str::before($nameInput, ':');
            $nameInput = Str::after($nameInput, ':');
        }

        $this->domain = match (true) {
            // Domain was specified explicitly via option (priority)
            filled($this->option('domain')) => new Domain($this->option('domain')),

            // Domain was specified as a prefix in the name
            filled($domainExtractedFromName) => new Domain($domainExtractedFromName),

            default => null,
        };

        // If the domain is not set, prompt for it
        if (! $this->domain) {
            $this->domain = new Domain($this->promptForDomainName());
        }

        // Now that the domain part is handled,
        // we will deal with the name portion.

        // Normalize slash and dot separators
        $nameInput = Str::replace(['.', '\\', '/'], '/', $nameInput);

        if ($this->nameIsAbsolute = Str::startsWith($nameInput, ['/'])) {
            // $nameInput = Str::after($nameInput, '/');
        }

        $this->input->setArgument('name', $nameInput);

        app('ddd')->captureCommandContext($this, $this->domain, $this->guessObjectType());
    }
}
