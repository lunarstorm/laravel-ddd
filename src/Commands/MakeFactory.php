<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_NONE, 'The model to associate to the factory'],
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
        $nameWithDomain = $domain . '/' . $this->getNameInput();
        $model = $this->option('model');

        // Generate the factory using the native factory generator
        $this->call(FactoryMakeCommand::class, [
            'name' => $nameWithDomain,
            '--model' => $model ?: false,
        ]);

        // Correct the model namespace inside the generated factory.
        $pathToFactory = base_path("database/factories/{$nameWithDomain}.php");

        $contents = file_get_contents($pathToFactory);

        $contents = str_replace(
            "\\App\\{$model}",
            "\\{$model}",
            $contents
        );

        file_put_contents($pathToFactory, $contents);
    }
}
