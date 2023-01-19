<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class MakeModel extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:model {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a domain model';

    protected $type = 'Model';

    protected function getStub()
    {
        return $this->resolveStubPath('components/model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.models', 'Models');
    }

    public function handle()
    {
        $baseModel = config('ddd.base_model');

        $parts = str($baseModel)->explode('\\');
        $baseModelName = $parts->last();
        $baseModelPath = $this->getPath($baseModel);
        // dd($baseModelPath);

        if (!file_exists($baseModelPath)) {
            $this->warn("Base model {$baseModel} doesn't exist, generating...");

            // dd($baseModel, $baseModelName);

            $this->call(MakeBaseModel::class, [
                'domain' => "Shared",
                'name' => $baseModelName,
            ]);
        }
        else{
            // dd('file exists', $baseModelPath);
        }

        parent::handle();
    }
}
