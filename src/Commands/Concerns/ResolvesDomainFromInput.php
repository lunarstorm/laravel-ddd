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
                'ddd:cast' => $this->domain->namespace->casts,
                'ddd:command' => $this->domain->namespace->commands,
                'ddd:enum' => $this->domain->namespace->enums,
                'ddd:event' => $this->domain->namespace->events,
                'ddd:exception' => $this->domain->namespace->exceptions,
                'ddd:job' => $this->domain->namespace->jobs,
                'ddd:mail' => $this->domain->namespace->mail,
                'ddd:notification' => $this->domain->namespace->notifications,
                'ddd:resource' => $this->domain->namespace->resources,
                'ddd:rule' => $this->domain->namespace->rules,
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
                    'ddd:cast' => $this->domain->cast($name)->path,
                    'ddd:command' => $this->domain->command($name)->path,
                    'ddd:enum' => $this->domain->enum($name)->path,
                    'ddd:event' => $this->domain->event($name)->path,
                    'ddd:exception' => $this->domain->exception($name)->path,
                    'ddd:job' => $this->domain->job($name)->path,
                    'ddd:mail' => $this->domain->mail($name)->path,
                    'ddd:notification' => $this->domain->notification($name)->path,
                    'ddd:resource' => $this->domain->resource($name)->path,
                    'ddd:rule' => $this->domain->rule($name)->path,
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
