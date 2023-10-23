<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

abstract class DomainGeneratorCommand extends GeneratorCommand
{
    protected function getArguments()
    {
        return [
            new InputArgument(
                'domain',
                InputArgument::REQUIRED,
                'The domain'
            ),
        ];
    }

    protected function rootNamespace()
    {
        return str($this->getDomainBasePath())
            ->rtrim('/\\')
            ->basename()
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = $this->getDomain();

        return $rootNamespace.'\\'.$domain.'\\'.$this->getRelativeDomainNamespace();
    }

    abstract protected function getRelativeDomainNamespace(): string;

    protected function getNameInput()
    {
        return Str::studly($this->argument('name'));
    }

    protected function getDomainInput()
    {
        return $this->argument('domain');
    }

    protected function getDomain()
    {
        return str($this->getDomainInput())
            ->trim()
            ->replace(['.', '/'], '\\')
            ->studly()
            ->toString();
    }

    protected function getDomainBasePath()
    {
        return $this->laravel->basePath(config('ddd.paths.domains', 'src/Domains'));
    }

    protected function getPath($name)
    {
        $name = str($name)
            ->replaceFirst($this->rootNamespace(), '')
            ->replace('\\', '/')
            ->ltrim('/')
            ->append('.php')
            ->toString();

        return $this->getDomainBasePath().'/'.$name;
    }

    protected function resolveStubPath($path)
    {
        $path = ltrim($path, '/\\');

        $publishedPath = resource_path('stubs/ddd/'.$path);

        return file_exists($publishedPath)
            ? $publishedPath
            : __DIR__.DIRECTORY_SEPARATOR.'../../stubs'.DIRECTORY_SEPARATOR.$path;
    }

    protected function fillPlaceholder($stub, $placeholder, $value)
    {
        return str_replace(["{{$placeholder}}", "{{ $placeholder }}"], $value, $stub);
    }

    protected function preparePlaceholders(): array
    {
        return [];
    }

    protected function applyPlaceholders($stub)
    {
        $placeholders = $this->preparePlaceholders();

        foreach ($placeholders as $placeholder => $value) {
            $stub = $this->fillPlaceholder($stub, $placeholder, $value ?? '');
        }

        return $stub;
    }

    protected function buildClass($name)
    {
        return $this->applyPlaceholders(parent::buildClass($name));
    }
}
