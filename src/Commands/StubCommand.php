<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\StubPublishCommand;
use Illuminate\Foundation\Events\PublishingStubs;
use Illuminate\Support\Str;
use ReflectionClass;
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

    protected function getNativeLaravelStubs()
    {
        $laravelStubCommand = new ReflectionClass(new StubPublishCommand);

        $dir = dirname($laravelStubCommand->getFileName());

        return [
            $dir.'/stubs/cast.inbound.stub' => 'cast.inbound.stub',
            $dir.'/stubs/cast.stub' => 'cast.stub',
            $dir.'/stubs/class.stub' => 'class.stub',
            $dir.'/stubs/class.invokable.stub' => 'class.invokable.stub',
            $dir.'/stubs/console.stub' => 'console.stub',
            $dir.'/stubs/enum.stub' => 'enum.stub',
            $dir.'/stubs/enum.backed.stub' => 'enum.backed.stub',
            $dir.'/stubs/event.stub' => 'event.stub',
            $dir.'/stubs/job.queued.stub' => 'job.queued.stub',
            $dir.'/stubs/job.stub' => 'job.stub',
            $dir.'/stubs/listener.typed.queued.stub' => 'listener.typed.queued.stub',
            $dir.'/stubs/listener.queued.stub' => 'listener.queued.stub',
            $dir.'/stubs/listener.typed.stub' => 'listener.typed.stub',
            $dir.'/stubs/listener.stub' => 'listener.stub',
            $dir.'/stubs/mail.stub' => 'mail.stub',
            $dir.'/stubs/markdown-mail.stub' => 'markdown-mail.stub',
            $dir.'/stubs/markdown-notification.stub' => 'markdown-notification.stub',
            $dir.'/stubs/model.pivot.stub' => 'model.pivot.stub',
            $dir.'/stubs/model.stub' => 'model.stub',
            $dir.'/stubs/notification.stub' => 'notification.stub',
            $dir.'/stubs/observer.plain.stub' => 'observer.plain.stub',
            $dir.'/stubs/observer.stub' => 'observer.stub',
            $dir.'/stubs/pest.stub' => 'pest.stub',
            $dir.'/stubs/pest.unit.stub' => 'pest.unit.stub',
            $dir.'/stubs/policy.plain.stub' => 'policy.plain.stub',
            $dir.'/stubs/policy.stub' => 'policy.stub',
            $dir.'/stubs/provider.stub' => 'provider.stub',
            $dir.'/stubs/request.stub' => 'request.stub',
            $dir.'/stubs/resource.stub' => 'resource.stub',
            $dir.'/stubs/resource-collection.stub' => 'resource-collection.stub',
            $dir.'/stubs/rule.stub' => 'rule.stub',
            $dir.'/stubs/scope.stub' => 'scope.stub',
            $dir.'/stubs/test.stub' => 'test.stub',
            $dir.'/stubs/test.unit.stub' => 'test.unit.stub',
            $dir.'/stubs/trait.stub' => 'trait.stub',
            $dir.'/stubs/view-component.stub' => 'view-component.stub',
            // realpath($dir . '/../../Database/Console/Factories/stubs/factory.stub') => 'factory.stub',
            realpath($dir.'/../../Database/Console/Seeds/stubs/seeder.stub') => 'seeder.stub',
            realpath($dir.'/../../Database/Migrations/stubs/migration.create.stub') => 'migration.create.stub',
            realpath($dir.'/../../Database/Migrations/stubs/migration.stub') => 'migration.stub',
            realpath($dir.'/../../Database/Migrations/stubs/migration.update.stub') => 'migration.update.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.api.stub') => 'controller.api.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.invokable.stub') => 'controller.invokable.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.model.api.stub') => 'controller.model.api.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.model.stub') => 'controller.model.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.nested.api.stub') => 'controller.nested.api.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.nested.singleton.api.stub') => 'controller.nested.singleton.api.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.nested.singleton.stub') => 'controller.nested.singleton.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.nested.stub') => 'controller.nested.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.plain.stub') => 'controller.plain.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.singleton.api.stub') => 'controller.singleton.api.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.singleton.stub') => 'controller.singleton.stub',
            realpath($dir.'/../../Routing/Console/stubs/controller.stub') => 'controller.stub',
            realpath($dir.'/../../Routing/Console/stubs/middleware.stub') => 'middleware.stub',
        ];
    }

    protected function resolveSelectedStubs(array $names = [])
    {
        $stubs = [
            realpath(__DIR__.'/../../stubs/action.stub') => 'action.stub',
            realpath(__DIR__.'/../../stubs/dto.stub') => 'dto.stub',
            realpath(__DIR__.'/../../stubs/value-object.stub') => 'value-object.stub',
            realpath(__DIR__.'/../../stubs/view-model.stub') => 'view-model.stub',
            realpath(__DIR__.'/../../stubs/base-view-model.stub') => 'base-view-model.stub',
            realpath(__DIR__.'/../../stubs/factory.stub') => 'factory.stub',
            ...$this->getNativeLaravelStubs(),
        ];

        if ($names) {
            return collect($stubs)
                ->filter(
                    fn ($stub, $path) => in_array($stub, $names)
                        || in_array(str($stub)->replaceEnd('.stub', '')->toString(), $names)
                )
                ->all();
        }

        return multisearch(
            label: 'Which stub should be published?',
            placeholder: 'Search for a stub...',
            options: fn (string $value) => strlen($value) > 0
                ? collect($stubs)->filter(fn ($stub, $path) => str($stub)->contains($value))->all()
                : $stubs,
            required: true
        );
    }

    public function handle(): int
    {
        $option = match (true) {
            $this->option('all') => 'all',
            count($this->argument('name')) > 0 => 'named',
            default => select(
                label: 'What do you want to do?',
                options: [
                    'all' => 'Publish all stubs',
                    'some' => 'Choose stubs to publish',
                ],
                required: true,
                default: 'all'
            )
        };

        if ($option === 'all') {
            $this->comment('Publishing all stubs...');

            $this->call('vendor:publish', [
                '--tag' => 'ddd-stubs',
            ]);

            return self::SUCCESS;
        }

        $stubs = $this->resolveSelectedStubs($this->argument('name'));

        if (! is_dir($stubsPath = $this->laravel->basePath('stubs/ddd'))) {
            (new Filesystem)->makeDirectory($stubsPath, recursive: true);
        }

        if (empty($stubs)) {
            $this->warn('No matching stubs found.');

            return self::INVALID;
        }

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
