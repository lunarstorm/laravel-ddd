<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Input\InputOption;

class DomainSeederMakeCommand extends SeederMakeCommand
{
    use ResolvesDomainFromInput {
        ResolvesDomainFromInput::getPath as getDomainPath;
    }
    use ResolvesStubPath;

    protected $name = 'ddd:seed';

    protected $description = 'Generate a domain seeder';

    protected function getStub()
    {
        return $this->resolveStubPath('seeder.php.stub');
    }

    protected function getPath($name)
    {
        if (! str_ends_with($name, 'Seeder')) {
            $name .= 'Seeder';
        }

        return $this->getDomainPath($name);
    }
}
