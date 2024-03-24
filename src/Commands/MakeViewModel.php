<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class MakeViewModel extends DomainGeneratorCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:view-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a view model';

    protected $type = 'View Model';

    protected function getStub()
    {
        return $this->resolveStubPath('view-model.php.stub');
    }

    public function handle()
    {
        $baseViewModel = config('ddd.base_view_model');

        $parts = str($baseViewModel)->explode('\\');
        $baseName = $parts->last();
        $basePath = $this->getPath($baseViewModel);

        if (! file_exists($basePath)) {
            $this->warn("Base view model {$baseViewModel} doesn't exist, generating...");

            $this->call(MakeBaseViewModel::class, [
                'domain' => 'Shared',
                'name' => $baseName,
            ]);
        }

        parent::handle();
    }
}
