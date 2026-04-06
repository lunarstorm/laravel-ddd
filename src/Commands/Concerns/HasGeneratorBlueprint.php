<?php

namespace Tey\LaravelDDD\Commands\Concerns;

use Tey\LaravelDDD\Support\GeneratorBlueprint;

trait HasGeneratorBlueprint
{
    protected ?GeneratorBlueprint $blueprint = null;
}
