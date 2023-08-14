<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeFactory extends DomainGeneratorCommand
{
    protected $name = 'ddd:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a domain model factory';

    protected $type = 'Factory';

    protected function getArguments()
    {
        return [
            ...parent::getArguments(),

            new InputArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the factory',
            ),
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('factory.php.stub');
    }

    protected function rootNamespace()
    {
        return 'Database\\Factories\\';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = $this->getDomain();

        return $rootNamespace.'\\'.$domain;
    }

    protected function getRelativeDomainNamespace(): string
    {
        return '';
    }

    public function handle()
    {
        $domain = $this->getDomain();
        $name = $domain.'/'.$this->getNameInput();

        // Generate the factory using the native factory generator
        // with the name prefixed with the domain subdirectory.
        $this->call(FactoryMakeCommand::class, [
            'name' => $name,
        ]);
    }
}
