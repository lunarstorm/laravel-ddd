<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\multiselect;

class PublishCommand extends Command
{
    public $signature = 'ddd:publish';

    protected $description = 'Publish package resources';

    protected function getOptions()
    {
        return [
            ['config', 'c', InputOption::VALUE_NONE, 'Publish the config file'],
            ['stubs', 's', InputOption::VALUE_NONE, 'Publish the stubs'],
        ];
    }

    protected function askForThingsToPublish()
    {
        $options = [
            'stubs' => 'Stubs',
            'config' => 'Config File',
        ];

        return multiselect(
            label: 'What should be published?',
            options: $options,
            required: true
        );
    }

    public function handle(): int
    {
        $thingsToPublish = [
            ...$this->hasOption('config') ? ['config'] : [],
            ...$this->hasOption('stubs') ? ['stubs'] : [],
        ] ?: $this->askForThingsToPublish();

        if (in_array('config', $thingsToPublish)) {
            $this->comment('Publishing config...');
            $this->call('vendor:publish', [
                '--tag' => 'ddd-config',
            ]);
        }

        if (in_array('stubs', $thingsToPublish)) {
            $this->comment('Publishing stubs...');

            $this->callSilently('vendor:publish', [
                '--tag' => 'ddd-stubs',
            ]);
        }

        return self::SUCCESS;
    }
}
