<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

use function Laravel\Prompts\suggest;

trait CanPromptForDomain
{
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
}
