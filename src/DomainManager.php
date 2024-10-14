<?php

namespace Lunarstorm\LaravelDDD;

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
    protected $applicationLayerNamespaceResolver;

    public function __construct()
    {
        $this->autoloadFilter = null;
        $this->applicationLayerFilter = null;
        $this->applicationLayerNamespaceResolver = null;
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

    public function resolveApplicationLayerNamespaceUsing(callable $resolver): void
    {
        $this->applicationLayerNamespaceResolver = $resolver;
    }

    public function getApplicationLayerNamespaceResolver(): ?callable
    {
        return $this->applicationLayerNamespaceResolver;
    }
}
