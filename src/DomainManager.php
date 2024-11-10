<?php

namespace Lunarstorm\LaravelDDD;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\Path;
use Lunarstorm\LaravelDDD\ValueObjects\DomainCommandContext;

class DomainManager
{
    /**
     * The autoload path filter callback.
     *
     * @var callable|null
     */
    protected $autoloadFilter;

    /**
     * The application layer filter callback.
     *
     * @var callable|null
     */
    protected $applicationLayerFilter;

    /**
     * The application layer object resolver callback.
     *
     * @var callable|null
     */
    protected $namespaceResolver;

    protected ?DomainCommandContext $commandContext;

    protected StubManager $stubs;

    public function __construct()
    {
        $this->autoloadFilter = null;
        $this->applicationLayerFilter = null;
        $this->namespaceResolver = null;
        $this->commandContext = null;
        $this->stubs = new StubManager;
    }

    public function filterAutoloadPathsUsing(callable $filter): void
    {
        $this->autoloadFilter = $filter;
    }

    public function getAutoloadFilter(): ?callable
    {
        return $this->autoloadFilter;
    }

    public function filterApplicationLayerUsing(callable $filter): void
    {
        $this->applicationLayerFilter = $filter;
    }

    public function getApplicationLayerFilter(): ?callable
    {
        return $this->applicationLayerFilter;
    }

    public function resolveNamespaceUsing(callable $resolver): void
    {
        $this->namespaceResolver = $resolver;
    }

    public function getNamespaceResolver(): ?callable
    {
        return $this->namespaceResolver;
    }

    public function captureCommandContext(Command $command, ?Domain $domain, ?string $type): void
    {
        $this->commandContext = DomainCommandContext::fromCommand($command, $domain, $type);
    }

    public function getCommandContext(): ?DomainCommandContext
    {
        return $this->commandContext;
    }

    public function packagePath($path = ''): string
    {
        return Path::normalize(__DIR__.'/../'.$path);
    }

    public function laravelVersion($value)
    {
        return version_compare(app()->version(), $value, '>=');
    }

    public function stubs(): StubManager
    {
        return $this->stubs;
    }
}
