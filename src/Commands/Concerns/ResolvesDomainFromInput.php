<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\GeneratorBlueprint;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\suggest;

trait ResolvesDomainFromInput
{
    use HandleHooks,
        HasGeneratorBlueprint,
        QualifiesDomainModels;

    protected $nameIsAbsolute = false;

    protected function getOptions()
    {
        return [
            ...parent::getOptions(),
            ['domain', null, InputOption::VALUE_OPTIONAL, 'The domain name'],
        ];
    }

    protected function rootNamespace()
    {
        return $this->blueprint->rootNamespace();
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->blueprint
            ? $this->blueprint->getDefaultNamespace($rootNamespace)
            : parent::getDefaultNamespace($rootNamespace);
    }

    protected function getPath($name)
    {
        return $this->blueprint
            ? $this->blueprint->getPath($name)
            : parent::getPath($name);
    }

    protected function qualifyClass($name)
    {
        return $this->blueprint->qualifyClass($name);
    }

    protected function promptForDomainName(): string
    {
        $choices = collect(DomainResolver::domainChoices())
            ->mapWithKeys(fn ($name) => [Str::lower($name) => $name]);

        // Prompt for the domain
        $domainName = suggest(
            label: 'What is the domain?',
            options: fn ($value) => collect($choices)
                ->filter(fn ($name) => Str::contains($name, $value, ignoreCase: true))
                ->toArray(),
            placeholder: 'Start typing to search...',
            required: true
        );

        // Normalize the case of the domain name
        // if it is an existing domain.
        if ($match = $choices->get(Str::lower($domainName))) {
            $domainName = $match;
        }

        return $domainName;
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

        $domainName = match (true) {
            // Domain was specified explicitly via option (priority)
            filled($this->option('domain')) => $this->option('domain'),

            // Domain was specified as a prefix in the name
            filled($domainExtractedFromName) => $domainExtractedFromName,

            default => $this->promptForDomainName(),
        };

        $this->blueprint = new GeneratorBlueprint(
            commandName: $this->getName(),
            nameInput: $nameInput,
            domainName: $domainName,
            arguments: $this->arguments(),
            options: $this->options(),
        );

        $this->input->setArgument('name', $this->blueprint->nameInput);

        $this->input->setOption('domain', $this->blueprint->domainName);
    }
}
