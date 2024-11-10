<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Events\PublishingStubs;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\select;

class StubCommand extends Command
{
    public $name = 'ddd:stub';

    protected $description = 'Publish one or more stubs';

    protected function getArguments()
    {
        return [
            new InputArgument('name', InputArgument::IS_ARRAY, 'One or more names of specific stubs to publish'),
        ];
    }

    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Publish all stubs'],
            ['existing', null, InputOption::VALUE_NONE, 'Publish and overwrite only the files that have already been published'],
            ['force', null, InputOption::VALUE_NONE, 'Overwrite any existing files'],
        ];
    }

    protected function getStubChoices()
    {
        return [
            ...app('ddd')->stubs()->dddStubs(),
            ...app('ddd')->stubs()->frameworkStubs(),
        ];
    }

    protected function resolveSelectedStubs(array $names = [])
    {
        $stubs = $this->getStubChoices();

        if ($names) {
            return collect($stubs)
                ->filter(
                    fn ($stub, $path) => in_array($stub, $names)
                        || in_array(str($stub)->replaceEnd('.stub', '')->toString(), $names)
                )
                ->all();
        }

        $selected = multisearch(
            label: 'Which stub should be published?',
            placeholder: 'Search for a stub...',
            options: fn (string $value) => strlen($value) > 0
                ? collect($stubs)->filter(fn ($stub, $path) => str($stub)->contains($value))->all()
                : $stubs,
            required: true
        );

        return collect($stubs)
            ->filter(fn ($stub, $path) => in_array($stub, $selected))
            ->all();
    }

    public function handle(): int
    {
        $option = match (true) {
            $this->option('all') => 'all',
            count($this->argument('name')) > 0 => 'named',
            default => select(
                label: 'What do you want to do?',
                options: [
                    'some' => 'Choose stubs to publish',
                    'all' => 'Publish all stubs',
                ],
                required: true,
                default: 'some'
            )
        };

        $stubs = $option === 'all'
            ? $this->getStubChoices()
            : $this->resolveSelectedStubs($this->argument('name'));

        if (empty($stubs)) {
            $this->warn('No matching stubs found.');

            return self::INVALID;
        }

        File::ensureDirectoryExists($stubsPath = $this->laravel->basePath('stubs/ddd'));

        $this->laravel['events']->dispatch($event = new PublishingStubs($stubs));

        foreach ($event->stubs as $from => $to) {
            $to = $stubsPath.DIRECTORY_SEPARATOR.ltrim($to, DIRECTORY_SEPARATOR);

            $relativePath = Str::after($to, $this->laravel->basePath());

            $this->info("Publishing {$relativePath}");

            if ((! $this->option('existing') && (! file_exists($to) || $this->option('force')))
                || ($this->option('existing') && file_exists($to))
            ) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->components->info('Stubs published successfully.');

        return self::SUCCESS;
    }
}
