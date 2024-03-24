<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
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

    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->domain) {
            return match ($this->name) {
                'make:cast' => $this->domain->namespace->casts,
                'make:command' => $this->domain->namespace->commands,
                'make:enum' => $this->domain->namespace->enums,
                'make:event' => $this->domain->namespace->events,
                'make:exception' => $this->domain->namespace->exceptions,
                'make:job' => $this->domain->namespace->jobs,
                'make:mail' => $this->domain->namespace->mail,
                'make:notification' => $this->domain->namespace->notifications,
                'make:resource' => $this->domain->namespace->resources,
                'make:rule' => $this->domain->namespace->rules,
                default => throw new \Exception("Unsupported domain generator: {$this->name}"),
            };
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function getPath($name)
    {
        if ($this->domain) {
            return $this->laravel->basePath(
                match ($this->name) {
                    'make:cast' => $this->domain->cast($name)->path,
                    'make:command' => $this->domain->command($name)->path,
                    'make:enum' => $this->domain->enum($name)->path,
                    'make:event' => $this->domain->event($name)->path,
                    'make:exception' => $this->domain->exception($name)->path,
                    'make:job' => $this->domain->job($name)->path,
                    'make:mail' => $this->domain->mail($name)->path,
                    'make:notification' => $this->domain->notification($name)->path,
                    'make:resource' => $this->domain->resource($name)->path,
                    'make:rule' => $this->domain->rule($name)->path,
                    default => throw new \Exception("Unsupported domain generator: {$this->name}"),
                }
            );
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
