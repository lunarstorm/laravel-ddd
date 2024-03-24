<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\Path;
use Symfony\Component\Console\Input\InputOption;

trait ResolvesDomainFromInput
{
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
        return Str::finish(DomainResolver::getConfiguredDomainNamespace(), '\\');
    }

    protected function guessObjectType(): string
    {
        $type = str($this->name)->after(':')->snake()->toString();

        return $type;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->domain) {
            return $this->domain->namespaceFor($this->guessObjectType());
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function getPath($name)
    {
        if ($this->domain) {
            return Path::normalize($this->laravel->basePath(
                $this->domain->object($this->guessObjectType(), class_basename($name))->path
            ));
        }

        return parent::getPath($name);
    }

    public function handle()
    {
        $nameInput = $this->argument('name');

        // If the name contains a domain prefix, extract it
        // and strip it from the name argument.
        if ($domainExtractedFromName = Str::before($nameInput, ':')) {
            $this->input->setArgument('name', Str::after($nameInput, ':'));
        }

        $this->domain = match (true) {
            filled($this->option('domain')) => new Domain($this->option('domain')),
            filled($domainExtractedFromName) => new Domain($domainExtractedFromName),
            default => null,
        };

        parent::handle();
    }
}
