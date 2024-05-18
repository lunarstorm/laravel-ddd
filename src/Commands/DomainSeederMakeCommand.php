<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesStubPath;

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
