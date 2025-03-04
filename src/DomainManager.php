<?php

namespace Lunarstorm\LaravelDDD;

use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\GeneratorBlueprint;
use Lunarstorm\LaravelDDD\Support\Path;

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
     * The object schema resolver callback.
     *
     * @var callable|null
     */
    protected $objectSchemaResolver;

    /**
     * Resolved custom objects.
     */
    protected array $resolvedObjects = [];

    protected ?GeneratorBlueprint $commandContext;

    public function __construct()
    {
        $this->autoloadFilter = null;
        $this->applicationLayerFilter = null;
        $this->commandContext = null;
    }

    public function autoloader(): AutoloadManager
    {
        return app(AutoloadManager::class);
    }

    public function composer(): ComposerManager
    {
        return app(ComposerManager::class);
    }

    public function config(): ConfigManager
    {
        return app(ConfigManager::class);
    }

    public function stubs(): StubManager
    {
        return app(StubManager::class);
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

    public function resolveObjectSchemaUsing(callable $resolver): void
    {
        $this->objectSchemaResolver = $resolver;
    }

    public function getObjectSchemaResolver(): ?callable
    {
        return $this->objectSchemaResolver;
    }

    public function packagePath($path = ''): string
    {
        return Path::normalize(realpath(__DIR__.'/../'.$path));
    }

    public function laravelVersion($value)
    {
        return version_compare(app()->version(), $value, '>=');
    }
}
