<?php

namespace Lunarstorm\LaravelDDD\Tests\Fixtures\Enums;

enum Feature: string
{
    case PromptForMissingInput = '9.49.0';
    case IncludeFilepathInGeneratorCommandOutput = '9.32.0';
    case Laravel11 = '11.0.0';
    case LaravelPromptsPackage = '10.17';
    case LaravelPackageOptimizeCommands = '11.27.1';
    case PromptMultiSearchAssertion = '11.30.0';

    public function exists(): bool
    {
        return version_compare(app()->version(), $this->value, '>=');
    }

    public function missing(): bool
    {
        return ! $this->exists();
    }
}
