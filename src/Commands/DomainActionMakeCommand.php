<?php

namespace Lunarstorm\LaravelDDD\Commands;

class DomainActionMakeCommand extends DomainGeneratorCommand
{
    protected $name = 'ddd:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an action';

    protected $type = 'Action';

    protected function getStub()
    {
        return $this->resolveDddStubPath('action.stub');
    }

    protected function preparePlaceholders(): array
    {
        $baseClass = config('ddd.base_action');

        return [
            'extends' => filled($baseClass) ? " extends {$baseClass}" : '',
        ];
    }
}
