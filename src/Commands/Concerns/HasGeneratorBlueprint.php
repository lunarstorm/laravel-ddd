<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Lunarstorm\LaravelDDD\Support\GeneratorBlueprint;

trait HasGeneratorBlueprint
{
    protected ?GeneratorBlueprint $blueprint = null;
}
