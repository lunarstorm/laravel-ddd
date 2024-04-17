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

    public function __construct()
    {
        $this->autoloadFilter = null;
    }

    public function filterAutoloadPathsUsing(callable $filter): void
    {
        $this->autoloadFilter = $filter;
    }

    public function getAutoloadFilter(): ?callable
    {
        return $this->autoloadFilter;
    }
}
