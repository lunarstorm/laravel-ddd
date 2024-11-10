<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\multiselect;

class PublishCommand extends Command
{
    public $name = 'ddd:publish';

    protected $description = 'Publish package resources';

    protected function getOptions()
    {
        return [
            ['config', 'c', InputOption::VALUE_NONE, 'Publish the config file'],
            ['stubs', 's', InputOption::VALUE_NONE, 'Publish the stubs'],
            ['all', 'a', InputOption::VALUE_NONE, 'Publish both the config file and stubs'],
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
            ...$this->option('config') ? ['config'] : [],
            ...$this->option('stubs') ? ['stubs'] : [],
            ...$this->option('all') ? ['config', 'stubs'] : [],
        ] ?: $this->askForThingsToPublish();

        if (in_array('config', $thingsToPublish)) {
            $this->comment('Publishing config...');
            $this->call('vendor:publish', [
                '--tag' => 'ddd-config',
            ]);
        }

        if (in_array('stubs', $thingsToPublish)) {
            $this->comment('Publishing stubs...');
            $this->call('ddd:stub', [
                '--all' => true,
            ]);
        }

        return self::SUCCESS;
    }
}
