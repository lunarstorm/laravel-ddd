<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

class DomainRequestMakeCommand extends RequestMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:request';

    // protected function getDefaultNamespace($rootNamespace)
    // {
    //     return $rootNamespace.'\Http\Requests';
    // }

    protected function rootNamespace()
    {
        $type = $this->guessObjectType();

        return Str::finish(DomainResolver::resolveRootNamespace($type), '\\');
    }
}
